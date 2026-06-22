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
use ArekX\PQL\Drivers\Pdo\Sqlite\SqliteDriver;
use ArekX\PQL\RawQueryResult;
use Codeception\Test\Unit;
use PDO;

class PdoDriverConfigureTest extends Unit
{
    public function testConfigureAppliesOptionsAndFetchMode()
    {
        $options = [PDO::ATTR_TIMEOUT => 7];

        $driver = MySqlDriver::create([
            'dsn' => 'mysql:host=127.0.0.1',
            'username' => 'u',
            'password' => 'p',
            'options' => $options,
            'fetchMode' => PDO::FETCH_OBJ,
        ]);

        expect($driver->options)->toBe($options);
        expect($driver->fetchMode)->toBe(PDO::FETCH_OBJ);
    }

    public function testSecondConfigureOverridesPrevious()
    {
        $driver = MySqlDriver::create([
            'dsn' => 'mysql:host=127.0.0.1',
            'username' => 'u',
            'password' => 'p',
            'options' => [PDO::ATTR_TIMEOUT => 7],
            'fetchMode' => PDO::FETCH_OBJ,
        ]);

        $driver->configure([
            'options' => [PDO::ATTR_PERSISTENT => true],
            'fetchMode' => PDO::FETCH_BOTH,
        ]);

        expect($driver->options)->toBe([PDO::ATTR_PERSISTENT => true]);
        expect($driver->fetchMode)->toBe(PDO::FETCH_BOTH);
    }

    public function testReconfigureKeepsValuesNotSuppliedAgain()
    {
        $driver = MySqlDriver::create([
            'dsn' => 'mysql:host=127.0.0.1',
            'username' => 'u',
            'password' => 'p',
        ]);

        $driver->configure(['fetchMode' => PDO::FETCH_NUM]);

        expect($driver->dsn)->toBe('mysql:host=127.0.0.1');
        expect($driver->fetchMode)->toBe(PDO::FETCH_NUM);
    }

    public function testFetchModeIsForwardedToPdo()
    {
        $driver = SqliteDriver::create([
            'dsn' => 'sqlite::memory:',
            'fetchMode' => PDO::FETCH_NUM,
        ]);

        $row = $driver->fetchFirst(RawQueryResult::create('SELECT 1 AS a'));

        // FETCH_NUM gives a numerically indexed row, not an associative one.
        expect($row)->toBe([0 => 1]);
    }

    public function testOptionsAreForwardedToPdo()
    {
        $driver = SqliteDriver::create([
            'dsn' => 'sqlite::memory:',
            'options' => [PDO::ATTR_CASE => PDO::CASE_UPPER],
            'fetchMode' => PDO::FETCH_ASSOC,
        ]);

        $row = $driver->fetchFirst(RawQueryResult::create('SELECT 1 AS a'));

        // ATTR_CASE is only honoured if the options reached the PDO constructor.
        expect($row)->toBe(['A' => 1]);
    }
}
