<?php
/**
 * Copyright 2021 Aleksandar Panic
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace integration\Pdo\MySql;

use ArekX\PQL\Drivers\Pdo\MySql\MySqlDriver;
use ArekX\PQL\Drivers\Pdo\MySql\MySqlQueryBuilder;
use ArekX\PQL\QueryRunner;
use ArekX\PQL\RawQueryResult;
use function ArekX\PQL\Sql\insert;

class MySqlTestCase extends \Codeception\Test\Unit
{
    public function _before()
    {
        $query = file_get_contents(__DIR__ . '/data/test_db.sql');

        $driver = $this->createDriver();

        $driver->run(RawQueryResult::create($query));

        $builder = $this->createQueryBuilder();

        foreach ($this->fixtures() as $fixture) {
            ['to' => $table, 'data' => $items] = require $fixture;

            foreach ($items as $item) {
                $driver->run($builder->build(insert($table, $item)));
            }
        }
    }

    protected function getFixture($name)
    {
        $path = $this->fixtures()[$name];
        return require $path;
    }

    protected function createDriver(): MySqlDriver
    {
        return MySqlDriver::create([
            'dsn' => 'mysql:host=127.0.0.1;dbname=mysql_test',
            'username' => 'root',
            'password' => '',
        ]);
    }

    protected function createQueryBuilder(): MySqlQueryBuilder
    {
        return new MySqlQueryBuilder();
    }

    public function fixtures()
    {
        return [];
    }

    public function createRunner()
    {
        return QueryRunner::create($this->createDriver(), $this->createQueryBuilder());
    }
}