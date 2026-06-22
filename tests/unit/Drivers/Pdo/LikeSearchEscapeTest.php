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

use ArekX\PQL\Drivers\Pdo\Sqlite\SqliteDriver;
use ArekX\PQL\Drivers\Pdo\Sqlite\SqliteQueryBuilder;
use ArekX\PQL\RawQueryResult;
use ArekX\PQL\Sql\Query\Select;
use Codeception\Test\Unit;
use PDO;

use function ArekX\PQL\Sql\column;
use function ArekX\PQL\Sql\search;

class LikeSearchEscapeTest extends Unit
{
    private function driverWithRows(array $rows): SqliteDriver
    {
        $driver = SqliteDriver::create(['dsn' => 'sqlite::memory:', 'fetchMode' => PDO::FETCH_ASSOC]);
        $driver->run(RawQueryResult::create('CREATE TABLE t (v TEXT)'));

        foreach ($rows as $value) {
            $driver->run(RawQueryResult::create('INSERT INTO t (v) VALUES (:v)', [':v' => [$value, null]]));
        }

        return $driver;
    }

    private function searchValues(SqliteDriver $driver, string $needle): array
    {
        $query = Select::create()->columns('v')->from('t')->where(search(column('v'), $needle));
        $rows = $driver->fetchAll((new SqliteQueryBuilder())->build($query));

        return array_column($rows, 'v');
    }

    public function testPercentIsMatchedLiterally()
    {
        $driver = $this->driverWithRows(['100%', '100abc']);

        // Without escaping the % in "100%" would act as a wildcard and also
        // match "100abc". It must match only the literal "100%".
        expect($this->searchValues($driver, '100%'))->toBe(['100%']);
    }

    public function testUnderscoreIsMatchedLiterally()
    {
        $driver = $this->driverWithRows(['x_y', 'xqy']);

        // Without escaping the _ would match any single character and also
        // match "xqy". It must match only the literal "x_y".
        expect($this->searchValues($driver, 'x_y'))->toBe(['x_y']);
    }

    public function testEscapeClauseIsEmitted()
    {
        $query = Select::create()->columns('v')->from('t')->where(search(column('v'), '100%'));
        $raw = (new SqliteQueryBuilder())->build($query);

        expect($raw->getQuery())->toBe('SELECT "v" FROM "t" WHERE "v" LIKE :t0 ESCAPE \'~\'');
        expect($raw->getParams())->toBe([':t0' => ['%100~%%', null]]);
    }
}
