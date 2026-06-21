<?php

/**
 * Copyright 2026 Aleksandar Panic
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

namespace integration\Pdo\Sqlite;

use ArekX\PQL\Drivers\Pdo\Sqlite\SqliteDriver;
use ArekX\PQL\Drivers\Pdo\Sqlite\SqliteQueryBuilder;
use ArekX\PQL\QueryRunner;
use function ArekX\PQL\Sql\insert;

class SqliteTestCase extends \Codeception\Test\Unit
{
    public function _before()
    {
        // Start every test from a fresh database file so the schema (a single
        // CREATE statement) can be loaded without a DROP.
        $path = $this->databasePath();
        if (file_exists($path)) {
            unlink($path);
        }

        $driver = $this->createDriver();
        $driver->getPdo()->exec(file_get_contents(__DIR__ . '/data/test_db.sql'));

        $builder = $this->createQueryBuilder();

        foreach ($this->fixtures() as $fixture) {
            ['to' => $table, 'data' => $items] = require $fixture;

            foreach ($items as $item) {
                $driver->run($builder->build(insert($table, $item)));
            }
        }
    }

    protected function databasePath(): string
    {
        return sys_get_temp_dir() . '/pql_sqlite_test.db';
    }

    protected function getFixture($name)
    {
        $path = $this->fixtures()[$name];
        return require $path;
    }

    protected function createDriver(): SqliteDriver
    {
        return SqliteDriver::create([
            'dsn' => 'sqlite:' . $this->databasePath(),
        ]);
    }

    protected function createQueryBuilder(): SqliteQueryBuilder
    {
        return new SqliteQueryBuilder();
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
