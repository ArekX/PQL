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

namespace unit\Drivers\Pdo;

use ArekX\PQL\Drivers\Pdo\MySql\MySqlDriver;
use ArekX\PQL\Drivers\Pdo\MySql\MySqlQueryBuilder;
use ArekX\PQL\Drivers\Pdo\PdoDatabase;
use ArekX\PQL\Drivers\Pdo\Pgsql\PgsqlDriver;
use ArekX\PQL\Drivers\Pdo\Pgsql\PgsqlQueryBuilder;
use ArekX\PQL\Drivers\Pdo\Sqlite\SqliteDriver;
use ArekX\PQL\Drivers\Pdo\Sqlite\SqliteQueryBuilder;
use ArekX\PQL\Drivers\Pdo\SqlSrv\SqlSrvDriver;
use ArekX\PQL\Drivers\Pdo\SqlSrv\SqlSrvQueryBuilder;
use ArekX\PQL\QueryRunner;
use Codeception\Test\Unit;

class PdoDatabaseTest extends Unit
{
    public function testResolveSelectsDriverAndBuilderFromDsn()
    {
        $cases = [
            [['dsn' => 'mysql:host=127.0.0.1;dbname=test', 'username' => 'u', 'password' => 'p'], MySqlDriver::class, MySqlQueryBuilder::class],
            [['dsn' => 'pgsql:host=127.0.0.1;dbname=test', 'username' => 'u', 'password' => 'p'], PgsqlDriver::class, PgsqlQueryBuilder::class],
            [['dsn' => 'sqlite::memory:'], SqliteDriver::class, SqliteQueryBuilder::class],
            [['dsn' => 'sqlsrv:Server=127.0.0.1;Database=test', 'username' => 'u', 'password' => 'p'], SqlSrvDriver::class, SqlSrvQueryBuilder::class],
            [['dsn' => 'dblib:host=127.0.0.1;dbname=test', 'username' => 'u', 'password' => 'p'], SqlSrvDriver::class, SqlSrvQueryBuilder::class],
        ];

        foreach ($cases as [$config, $driverClass, $builderClass]) {
            $runner = PdoDatabase::resolve($config);

            expect($runner)->toBeInstanceOf(QueryRunner::class);
            expect($runner->driver)->toBeInstanceOf($driverClass);
            expect($runner->builder)->toBeInstanceOf($builderClass);
        }
    }

    public function testCreateDriverResolvesFromDsn()
    {
        $driver = PdoDatabase::createDriver(['dsn' => 'sqlite::memory:']);

        expect($driver)->toBeInstanceOf(SqliteDriver::class);
    }

    public function testCreateBuilderResolvesFromDsn()
    {
        $builder = PdoDatabase::createBuilder('pgsql:host=127.0.0.1;dbname=test');

        expect($builder)->toBeInstanceOf(PgsqlQueryBuilder::class);
    }

    public function testUnknownSchemeThrows()
    {
        $this->expectException(\UnexpectedValueException::class);
        PdoDatabase::resolve(['dsn' => 'unknown:host=127.0.0.1']);
    }

    public function testMissingDsnThrows()
    {
        $this->expectException(\InvalidArgumentException::class);
        PdoDatabase::resolve([]);
    }

    public function testSchemeMatchingIsCaseInsensitive()
    {
        $runner = PdoDatabase::resolve(['dsn' => 'SQLITE::memory:']);

        expect($runner->builder)->toBeInstanceOf(SqliteQueryBuilder::class);
    }

    public function testCustomSchemeCanBeRegistered()
    {
        $original = PdoDatabase::$drivers;

        try {
            PdoDatabase::$drivers['custom'] = [SqliteDriver::class, SqliteQueryBuilder::class];

            $runner = PdoDatabase::resolve(['dsn' => 'custom:anything']);

            expect($runner->driver)->toBeInstanceOf(SqliteDriver::class);
            expect($runner->builder)->toBeInstanceOf(SqliteQueryBuilder::class);
        } finally {
            PdoDatabase::$drivers = $original;
        }
    }
}
