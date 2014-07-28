#!/usr/bin/env php
<?php
namespace crodas\InfluxPHP\Client;

use crodas\InfluxPHP\Client;

require 'vendor/autoload.php';
ini_set ('memory_limit', '1024M');
define ('CHUNK_SIZE', 1000);

echo "
--------------------------------------------------
InfluxDB Bulk Data Import
--------------------------------------------------
";

//--------------------------------------------------------------------------------------------------------------------
// Handle command-line arguments
//--------------------------------------------------------------------------------------------------------------------

$set      = null;
$limit    = 0;
$fetching = true;
$env      = isset($_SERVER['ENV_NAME']) ? $_SERVER['ENV_NAME'] : 'local';
if ($argc > 1)
  do {
    switch ($argv[1]) {
      case '--env':
        $env = @array_splice ($argv, 1, 2)[1];
        break;
      case '--limit':
        $limit = @array_splice ($argv, 1, 2)[1];
        break;
      case '--set':
        $setArg = @array_splice ($argv, 1, 2)[1];
        $set    = json_decode ($setArg, true);
        if (!$set)
          fatal ("Invalid JSON for --set switch: $setArg");
        break;
      default:
        $fetching = false;
    }
  } while ($fetching && isset($argv[1]));

$file = @file_get_contents ('env-config.json');
if (!$file) fatal ("env-config.json not found");
$envConfig = json_decode ($file);
if (!$envConfig) fatal ("Invalid env-config.json");
if (!isset($envConfig->$env))
  fatal ("Invalid environment: $env");

$envCfg = $envConfig->$env;

echo "Environment:     $env
Target server:   $envCfg->host:$envCfg->port
";

$argc = count ($argv);

if ($argc != 4) {
  $envs = implode ('|', array_keys ((array)$envConfig));
  $me   = array_slice (explode ('/', $argv[0]), -1)[0];
  fatal ("Syntax: $me [--env $envs] [--limit N] [--set {json}] database series input-file.json");
}

list (, $database, $series, $file) = $argv;

$client = new Client($envCfg->host, $envCfg->port, $envCfg->user, $envCfg->pass);
$db     = $client->$database;

//--------------------------------------------------------------------------------------------------------------------
// Read input files
//--------------------------------------------------------------------------------------------------------------------

echo "Index:           $database
Type:            $series
Input file:      $file
";

$data = @file_get_contents ($file);
if (!$data)
  fatal ("File not found.");

$data = str_replace ("'", '"', $data);
$data = json_decode ($data, true);
if (!$data)
  fatal ("Invalid JSON data.");

if ($limit)
  $data = array_slice ($data, 0, $limit);

$c = count ($data);
echo "Record count:    $c
RAM used:        " . floor (memory_get_usage () / 1024 / 1024) . "MB
";

//--------------------------------------------------------------------------------------------------------------------
// Upload data
//--------------------------------------------------------------------------------------------------------------------

$chunks    = array_chunk ($data, CHUNK_SIZE);
$cc        = count ($chunks);
$lastP     = -1;
$startTime = time ();

echo "\nUploading data... ";

foreach ($chunks as $i => $chunk) {
  $p = floor ($i * 100 / $cc);
  if ($p != $lastP)
    echo ($lastP = $p) . "% ";
  if ($set) {
    $out = [];
    foreach ($chunk as $n => $r) {
      $r     = array_merge ($r, $set);
      $out[] = $r;
    }
    $db->insert ($series, $out);
  } else $db->insert ($series, $chunk);
}

$time = gmdate ("H:i:s", time () - $startTime);
echo "100%

Operação concluída com sucesso.
Duração total: $time

";
exit;

//--------------------------------------------------------------------------------------------------------------------
// Private
//--------------------------------------------------------------------------------------------------------------------

function fatal ($msg)
{
  die ("\n$msg\n\n");
}
