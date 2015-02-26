# InfluxDB Tools

Tools for working with InfluxDB databases.

## Installation

  1. Install `Composer` (if not yet installed).
  0. Run `composer install` in the project's directory.
  0. Modify `env-config.json` to suit your environment's configuration.
    - The environment variable `ENV_NAME` determines which environment you are in.
        - Valid values are those specified as keys in `env-config.json`.
    - Note: do NOT save the modified file back to the git repo.
  0. Run any tool without arguments for more information about the tool.
    - Ex. `./influxdb-import.php`

## Which tools are available?

Currently, only one tool is available.

### influxdb-import

Allows importing data in CSV format into an InfluxDB database.

##### Syntax

    influxdb-import.php [--env local|intranet|staging|production] [--limit N] [--set {json}] database series input-file.json

##### Command-line options

###### --env

Force a specific configuration environment.
If not specified, the environment name will be determined by the `ENV_NAME` environmental variable.
If no variable is defined, the name defaults to `local`.

###### --limit

Limit the maximum number of records to be imported.
If not specified, all records will be imported.

###### --set

Allows merging constant data into each record being imported.
The option's argument should be encoded as JSON.
You should escape spaces using `\`, otherwise a space will prematurely end the option's argument at that point.

## License

MIT

The MIT License (MIT)

Copyright (c) 2014 Impactwave Lda

Permission is hereby granted, free of charge, to any person obtaining a copy of
this software and associated documentation files (the "Software"), to deal in
the Software without restriction, including without limitation the rights to
use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of
the Software, and to permit persons to whom the Software is furnished to do so,
subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS
FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR
COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER
IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN
CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
