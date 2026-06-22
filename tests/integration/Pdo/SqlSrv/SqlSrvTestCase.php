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

namespace integration\Pdo\SqlSrv;

use ArekX\PQL\Drivers\Pdo\SqlSrv\SqlSrvDriver;
use ArekX\PQL\Drivers\Pdo\SqlSrv\SqlSrvQueryBuilder;
use ArekX\PQL\QueryRunner;
use function ArekX\PQL\Sql\insert;

class SqlSrvTestCase extends \Codeception\Test\Unit
{
    public function _before()
    {
        $driver = $this->createDriver();
        $pdo = $driver->getPdo();

        $pdo->exec(file_get_contents(__DIR__ . '/data/test_db.sql'));

        $builder = $this->createQueryBuilder();

        foreach ($this->fixtures() as $fixture) {
            ['to' => $table, 'data' => $items] = require $fixture;

            // The fixtures contain explicit identity values, which SQL Server
            // only allows while IDENTITY_INSERT is enabled for the table.
            $pdo->exec('SET IDENTITY_INSERT ' . $table . ' ON');

            foreach ($items as $item) {
                $driver->run($builder->build(insert($table, $item)));
            }

            $pdo->exec('SET IDENTITY_INSERT ' . $table . ' OFF');
        }
    }

    protected function getFixture($name)
    {
        $path = $this->fixtures()[$name];
        return require $path;
    }

    protected function createDriver(array $config = []): SqlSrvDriver
    {
        // The official sqlsrv driver returns numeric columns as strings by
        // default; this makes it return native numeric types so results match
        // across transports (FreeTDS already returns native types). The constant
        // only exists when the sqlsrv driver is loaded. Any options passed in are
        // merged on top.
        $options = [];
        if (defined('PDO::SQLSRV_ATTR_FETCHES_NUMERIC_TYPE')) {
            $options[constant('PDO::SQLSRV_ATTR_FETCHES_NUMERIC_TYPE')] = true;
        }

        if (!empty($config['options'])) {
            $options = $config['options'] + $options;
        }

        // The connection can be overridden via environment variables so the same
        // tests can run against either the FreeTDS (dblib) or the official
        // Microsoft (sqlsrv) PDO driver, e.g.:
        //   PQL_MSSQL_DSN='sqlsrv:Server=127.0.0.1,1433;Database=master;TrustServerCertificate=1'
        return SqlSrvDriver::create(array_merge([
            'dsn' => getenv('PQL_MSSQL_DSN') ?: 'dblib:host=127.0.0.1:1433;dbname=master',
            'username' => getenv('PQL_MSSQL_USERNAME') ?: 'sa',
            'password' => getenv('PQL_MSSQL_PASSWORD') ?: 'Str0ngP@ssw0rd!',
        ], $config, ['options' => $options]));
    }

    protected function createQueryBuilder(): SqlSrvQueryBuilder
    {
        return new SqlSrvQueryBuilder();
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
