# PQL

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/ArekX/PQL/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/ArekX/PQL/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/ArekX/PQL/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/ArekX/PQL/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/ArekX/PQL/badges/build.png?b=master)](https://scrutinizer-ci.com/g/ArekX/PQL/build-status/master)
[![Code Intelligence Status](https://scrutinizer-ci.com/g/ArekX/PQL/badges/code-intelligence.svg?b=master)](https://scrutinizer-ci.com/code-intelligence)
[![Documentation Status](https://readthedocs.org/projects/pql/badge/?version=latest)](https://pql.readthedocs.io/en/latest/?badge=latest)

PHP Database Query Library

This library is a database abstraction layer which abstracts the query commands (Select, Delete, Update, etc.) out from
the drivers (MySQL, Postgres, Microsoft SQL Server, etc.). Queries are defined in an eloquent way allowing you to write almost all
kinds of queries without having to rely on passing raw query data.

## Installation

Installing this library is done via composer `composer install arekxv/pql`

## Usage

Create a runner with `PdoDatabase::resolve()`. It picks the driver and query builder
automatically from the DSN scheme (`mysql:`, `pgsql:`, `sqlite:`, `sqlsrv:`):

```php
use ArekX\PQL\Drivers\Pdo\PdoDatabase;

$runner = PdoDatabase::resolve([
    'dsn' => 'mysql:host=127.0.0.1;dbname=your_database',
    'username' => 'username',
    'password' => 'password',
]);
```

See the driver pages for the supported DSNs and driver specific options:
[MySQL](docs/drivers/mysql.md) · [PostgreSQL](docs/drivers/pgsql.md) ·
[SQLite](docs/drivers/sqlite.md) · [Microsoft SQL Server](docs/drivers/sqlsrv.md). You can also wire the
driver and builder up by hand if you prefer (each driver page shows how).

Then write and run queries:

```php
use function \ArekX\PQL\Sql\{select, all, equal, column, value};

// Simple select
$query = select('*')
    ->from('user')
    ->where(all(['is_active' => 1]));

// SELECT * FROM `user` WHERE `is_active` = 1
$runner->fetchAll($query);


// Complex select with a sub-query
$query = select('*')
    ->from(['u' => 'user'])
    ->innerJoin(['r' => 'user_role'], 'u.role_id = r.id')
    ->where(['all', [
        'u.is_active' => 1,
        'r.id' => select('role_id')
            ->from('application_roles')
            ->where(equal(column('application_id'), value(2)))
    ]]);
/*
SELECT *
FROM `user` AS `u`
INNER JOIN `user_role` AS `r` ON u.role_id = r.id
WHERE
  `u`.`is_active` = 1
  AND `r`.`id` IN (
    SELECT `role_id` FROM `application_roles` WHERE `application_id` = 2
  )
*/
$runner->fetchAll($query);
```

## Documentation

Documentation is available in [here](docs/index.md) (in docs folder).

HTML version of the docs is available at: https://pql.readthedocs.io/


## Testing

Run `composer install` and then run `composer test`. This will run unit and integration tests.

For integration tests, database docker containers must be running otherwise those tests will fail.

To setup docker containers install docker and inside `tests` folder run `docker-compose up -d`.

To just run unit tests run `composer test-unit`.

For coverage report run `composer coverage` or you can take a look at
it [here](https://scrutinizer-ci.com/g/ArekX/PQL/?branch=master).

## License

Licensed under the Apache License, Version 2.0 (the "License"); you may not use this file except in compliance with the
License. You may obtain a copy of the License at

http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software distributed under the License is distributed on an "
AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the License for the specific
language governing permissions and limitations under the License.