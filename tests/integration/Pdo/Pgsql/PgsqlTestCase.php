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

namespace integration\Pdo\Pgsql;

use ArekX\PQL\Drivers\Pdo\Pgsql\PgsqlDriver;
use ArekX\PQL\Drivers\Pdo\Pgsql\PgsqlQueryBuilder;
use ArekX\PQL\QueryRunner;
use function ArekX\PQL\Sql\insert;

class PgsqlTestCase extends \Codeception\Test\Unit
{
    public function _before()
    {
        $query = file_get_contents(__DIR__ . '/data/test_db.sql');

        $driver = $this->createDriver();

        // PostgreSQL cannot run a multi-statement script (DROP + CREATE) through a
        // prepared statement, so the schema is loaded via the simple query protocol.
        $driver->getPdo()->exec($query);

        $builder = $this->createQueryBuilder();

        foreach ($this->fixtures() as $fixture) {
            ['to' => $table, 'data' => $items] = require $fixture;

            foreach ($items as $item) {
                $driver->run($builder->build(insert($table, $item)));
            }

            $this->resyncSequence($driver, $table);
        }
    }

    /**
     * Re-sync a table's serial sequence after loading fixtures.
     *
     * The fixtures insert explicit ids, and in PostgreSQL that does not advance
     * the underlying serial sequence. Without this, generated ids would restart
     * from 1 and collide with the fixtures.
     */
    protected function resyncSequence(PgsqlDriver $driver, string $table): void
    {
        $pdo = $driver->getPdo();

        $sequence = $pdo
            ->query('SELECT pg_get_serial_sequence(' . $pdo->quote($table) . ", 'id')")
            ->fetchColumn();

        if ($sequence) {
            $pdo->exec('SELECT setval(' . $pdo->quote($sequence) . ', (SELECT MAX(id) FROM ' . $table . '))');
        }
    }

    protected function getFixture($name)
    {
        $path = $this->fixtures()[$name];
        return require $path;
    }

    protected function createDriver(array $config = []): PgsqlDriver
    {
        return PgsqlDriver::create(array_merge([
            'dsn' => 'pgsql:host=127.0.0.1;port=5432;dbname=pgsql_test',
            'username' => 'postgres',
            'password' => 'postgres',
        ], $config));
    }

    protected function createQueryBuilder(): PgsqlQueryBuilder
    {
        return new PgsqlQueryBuilder();
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
