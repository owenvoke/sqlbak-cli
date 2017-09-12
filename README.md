# sqlbak-cli

A CLI script for backing up MySQL databases.

## Installation

Install via Composer:  
`composer global require pxgamer/sqlbak-cli`

Install using Git:  
`git clone git@github.com:PXgamer/sqlbak-cli.git`

## Configuration

Configuration can either be done using the `--` CLI options, or provided in a JSON file.

Standard naming is `.sqlbak.json`, and can be provided using `-c .sqlbak.json` when using a command.
```json
{
  "username": "root",
  "password": "root",
  "databases": [
    "test"
  ],
  "compress": true
}
```
