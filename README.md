# PQL

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/ArekX/PQL/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/ArekX/PQL/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/ArekX/PQL/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/ArekX/PQL/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/ArekX/PQL/badges/build.png?b=master)](https://scrutinizer-ci.com/g/ArekX/PQL/build-status/master)
[![Code Intelligence Status](https://scrutinizer-ci.com/g/ArekX/PQL/badges/code-intelligence.svg?b=master)](https://scrutinizer-ci.com/code-intelligence)
[![Documentation Status](https://readthedocs.org/projects/pql/badge/?version=latest)](https://pql.readthedocs.io/en/latest/?badge=latest)

PHP Database Query Library

This library is a database abstraction layer which abstracts the query commands (Select, Delete, Update, etc.) out from
the drivers (MySQL, Postgres, SQL Server, etc.). Queries are defined in an eloquent way allowing you to write almost all
kinds of queries without having to rely on passing raw query data.

## Installation

Installing this library is done via composer `composer install arekxv/pql`

## Usage

First you need to decide on which driver you will use. Following drivers are supported:

* [MySQL](docs/drivers/mysql.md)

After you decide on the driver, writing a query is as simple as:

```php
use function \ArekX\PQL\Sql\{select, all, equal, column, value};

// ... driver and builder initialization left out for brevity.

/** @var \ArekX\PQL\Contracts\Driver $driver */
/** @var \ArekX\PQL\Contracts\QueryBuilder $builder */

$runner = QueryRunner::create($driver, $builder);

// Fetching all results

$query = select('*') // or Select::create()->columns('*') if you do not want to use functions.
    ->from('user')
    ->where(all(['is_active' => 1]));

// Built query equals to: SELECT * FROM `user` WHERE `is_active` = 1;
$runner->fetchAll($query); // Returns all data for user table


// Complex select query:
$query = select('*')
    ->from(['u' => 'user'])
    ->innerJoin(['r' => 'user_role'], 'u.role_id = r.id')
    ->where(['all', [
         'u.is_active' => 1,
         'r.id' => select('role_id')
                    ->from('application_roles')
                    ->where(equal(column('application_id'), value(2)))
      ]);
/* 
Built query equals to:
SELECT 
    * 
FROM `user` AS `u`
INNER JOIN `user_role` AS `r` ON u.role_id = r.id
WHERE
 `u`.`is_active` = 1
 AND `r`.`id` IN (
    SELECT `role_id` FROM `application_roles` WHERE `application_id` = 2
 )
*/
$runner->fetchAll($query); // Returns all data for this query

```

## Documentation

Documentation is available in [here](docs/index.md) (in docs folder).

HTML version of the docs is available at: https://pql.readthedocs.io/

Generated API Reference is available at: https://pql.readthedocs.io/en/latest/api/

## Testing

Run `composer install` and then run `composer test`. This will run unit and integration tests.

For integration tests, database docker containers must be running otherwise those tests will fail.

To setup docker containers install docker and inside `tests` folder run `docker-compose up -d`.

To just run unit tests run `composer test-unit`.

For coverage report run `composer coverage` or you can take a look at
it [here](https://scrutinizer-ci.com/g/ArekX/PQL/?branch=master).

## License

Copyright 2021 Aleksandar Panic

Licensed under the Apache License, Version 2.0 (the "License"); you may not use this file except in compliance with the
License. You may obtain a copy of the License at

http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software distributed under the License is distributed on an "
AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the License for the specific
language governing permissions and limitations under the License.