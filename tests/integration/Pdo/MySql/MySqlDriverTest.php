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

use ArekX\PQL\Drivers\Pdo\PdoDriver;
use ArekX\PQL\QueryRunner;
use function ArekX\PQL\Sql\all;
use function ArekX\PQL\Sql\column;
use function ArekX\PQL\Sql\compare;
use function ArekX\PQL\Sql\equal;
use function ArekX\PQL\Sql\insert;
use function ArekX\PQL\Sql\raw;
use function ArekX\PQL\Sql\select;
use function ArekX\PQL\Sql\value;

class MySqlDriverTest extends MySqlTestCase
{
    public function fixtures(): array
    {
        return [
            'users' => __DIR__ . '/fixtures/users.php'
        ];
    }

    public function testPdoInstanceCreation()
    {
        $driver = $this->createDriver();
        $driver->charset = 'utf8mb4';
        $driver->emulatePrepare = true;

        $runner = QueryRunner::create($driver, $this->createQueryBuilder());

        $result = $runner->fetchFirst(select('*')->from('users'));

        $users = $this->getFixture('users');
        expect($result)->toBe($users['data'][0]);
        expect($driver->getPdo()->getAttribute(\PDO::ATTR_EMULATE_PREPARES))->toBe(1);
    }

    public function testFetchFirst()
    {
        $query = select('*')->from('users');
        $result = $this->createRunner()->fetchFirst($query);

        $users = $this->getFixture('users');

        expect($result)->toBe($users['data'][0]);
    }

    public function testFetchAll()
    {
        $query = select('*')->from('users');
        $result = $this->createRunner()->fetchAll($query);

        $users = $this->getFixture('users');

        expect($result)->toBe($users['data']);
    }

    public function testFetchReader()
    {
        $query = select('*')->from('users');
        $reader = $this->createRunner()->fetchReader($query);

        $users = $this->getFixture('users');

        expect($reader->getAllRows())->toBe($users['data']);
    }

    public function testFetchAllColumnRows()
    {
        $query = select('*')->from('users');
        $reader = $this->createRunner()->fetchReader($query);

        $users = $this->getFixture('users');

        $ids = array_map(fn($r) => $r['id'], $users['data']);

        expect($reader->getColumnRows())->toBe($ids);
    }

    public function testFetchWithReset()
    {
        $query = select('*')->from('users')->where(all(['id' => 1]));
        $reader = $this->createRunner()->fetchReader($query);

        $users = $this->getFixture('users');

        expect($reader->getNextRow())->toBe($users['data'][0]);
        expect($reader->getNextRow())->toBe(false);
        $reader->reset();
        expect($reader->getNextRow())->toBe($users['data'][0]);
        $reader->reset();
        expect($reader->getNextRow())->toBe($users['data'][0]);
        expect($reader->getNextRow())->toBe(false);
    }

    public function testTransactionCommit()
    {
        $query = select(raw('COUNT(*)'))->from('users');

        $driver = $this->createDriver();
        $runner = QueryRunner::create($driver, $this->createQueryBuilder());

        $countBefore = $runner->fetch($query)->scalar();

        $driver->beginTransaction()->execute(function() use ($runner) {
           $runner->run(insert("users", [
                'name' => 'Record 1',
                'username' => 'user',
                'password' => 'pass',
                'created_at' => '2021-09-20 16:51:56',
            ]));
        });

        $countAfter = $runner->fetch($query)->scalar();

        expect($countAfter)->toBe($countBefore + 1);
    }

    public function testTransactionMultipleCommitIsIgnored()
    {
        $query = select(raw('COUNT(*)'))->from('users');

        $driver = $this->createDriver();
        $runner = QueryRunner::create($driver, $this->createQueryBuilder());

        $countBefore = $runner->fetch($query)->scalar();

        $transaction = $driver->beginTransaction();

        $runner->run(insert("users", [
            'name' => 'Record 1',
            'username' => 'user',
            'password' => 'pass',
            'created_at' => '2021-09-20 16:51:56',
        ]));

        $transaction->commit();

        $runner->run(insert("users", [
            'name' => 'Record 1',
            'username' => 'user',
            'password' => 'pass',
            'created_at' => '2021-09-20 16:51:56',
        ]));

        $countAfter = $runner->fetch($query)->scalar();

        expect($countAfter)->toBe($countBefore + 2);

        $transaction->commit();

        $countAfter = $runner->fetch($query)->scalar();

        expect($countAfter)->toBe($countBefore + 2);
    }

    public function testTransactionMultipleRollbackIsIgnored()
    {
        $query = select(raw('COUNT(*)'))->from('users');

        $driver = $this->createDriver();
        $runner = QueryRunner::create($driver, $this->createQueryBuilder());

        $countBefore = $runner->fetch($query)->scalar();

        $transaction = $driver->beginTransaction();

        $runner->run(insert("users", [
            'name' => 'Record 1',
            'username' => 'user',
            'password' => 'pass',
            'created_at' => '2021-09-20 16:51:56',
        ]));

        $transaction->rollback();

        $runner->run(insert("users", [
            'name' => 'Record 1',
            'username' => 'user',
            'password' => 'pass',
            'created_at' => '2021-09-20 16:51:56',
        ]));

        $transaction->rollback();

        $countAfter = $runner->fetch($query)->scalar();

        expect($countAfter)->toBe($countBefore + 1);
    }

    public function testTransactionRollback()
    {
        $query = select(raw('COUNT(*)'))->from('users');

        $driver = $this->createDriver();
        $runner = QueryRunner::create($driver, $this->createQueryBuilder());

        $countBefore = $runner->fetch($query)->scalar();

        $transaction = $driver->beginTransaction();

        $runner->run(insert("users", [
            'name' => 'Record 1',
            'username' => 'user',
            'password' => 'pass',
            'created_at' => '2021-09-20 16:51:56',
        ]));

        $transaction->rollback();

        $countAfter = $runner->fetch($query)->scalar();

        expect($countAfter)->toBe($countBefore);
    }

    public function testTransactionException()
    {
        $driver = $this->createDriver();

        $this->expectException(\Exception::class);

        $driver->beginTransaction()->execute(function() {
           throw new \Exception('Exception test.');
        });
    }

    public function testFetch()
    {
        $query = select('*')->from('users');
        $reader = $this->createRunner()->fetch($query);

        $users = $this->getFixture('users');

        expect($reader->result())->toBe($users['data']);
    }

    public function testMiddleware()
    {
        $driver = $this->createDriver();
        $runner = QueryRunner::create($driver, $this->createQueryBuilder());

        $ranSteps = [];
        $driver->configure([
            'middleware' => [
                PdoDriver::STEP_OPEN => [
                    function ($result, $params, $next) use (&$ranSteps) {
                        $ranSteps[] = PdoDriver::STEP_OPEN;
                        return $next($result);
                    }
                ],
                PdoDriver::STEP_BEFORE_PREPARE => [
                    function ($result, $params, $next) use (&$ranSteps) {
                        $ranSteps[] = PdoDriver::STEP_BEFORE_PREPARE;
                        return $next($result);
                    }
                ],
                PdoDriver::STEP_AFTER_PREPARE => [
                    function ($result, $params, $next) use (&$ranSteps) {
                        $ranSteps[] = PdoDriver::STEP_AFTER_PREPARE;
                        return $next($result);
                    }
                ],
                PdoDriver::STEP_BEFORE_RUN => [
                    function ($result, $params, $next) use (&$ranSteps) {
                        $ranSteps[] = PdoDriver::STEP_BEFORE_RUN;
                        expect($result->getQuery())->toBe('SELECT * FROM `users`');
                        return $next($result);
                    }
                ],
                PdoDriver::STEP_AFTER_RUN => [
                    function ($result, $params, $next) use (&$ranSteps) {
                        $ranSteps[] = PdoDriver::STEP_AFTER_RUN;
                        return $next($result);
                    }
                ],
                PdoDriver::STEP_CLOSE => [
                    function ($result, $params, $next) use (&$ranSteps) {
                        $ranSteps[] = PdoDriver::STEP_CLOSE;
                        return $next($result);
                    }
                ],
            ]
        ]);

        $driver->close();

        $runner->fetchFirst(select('*')->from('users'));
        expect($ranSteps)->toBe([
            PdoDriver::STEP_BEFORE_RUN,
            PdoDriver::STEP_OPEN,
            PdoDriver::STEP_BEFORE_PREPARE,
            PdoDriver::STEP_AFTER_PREPARE,
            PdoDriver::STEP_AFTER_RUN
        ]);
    }


    public function testAppendMiddleware()
    {
        $driver = $this->createDriver();
        $runner = QueryRunner::create($driver, $this->createQueryBuilder());

        $ranSteps = [];

        $driver->appendMiddleware(PdoDriver::STEP_OPEN, function ($result, $params, $next) use (&$ranSteps) {
            $ranSteps[] = PdoDriver::STEP_OPEN;
            return $next($result);
        });

        $runner->fetchFirst(select('*')->from('users'));

        expect($ranSteps)->toBe([
            PdoDriver::STEP_OPEN,
        ]);
    }


    public function testCloseDriver()
    {
        $driver = $this->createDriver();

        $ranSteps = [];
        $driver->configure([
            'middleware' => [
                PdoDriver::STEP_CLOSE => [
                    function ($result, $params, $next) use (&$ranSteps) {
                        $ranSteps[] = PdoDriver::STEP_CLOSE;
                        return $next($result);
                    }
                ],
            ]
        ]);

        $driver->open();
        $driver->close();
        $driver->close();

        expect($ranSteps)->toBe([
            PdoDriver::STEP_CLOSE
        ]);
    }

    public function testGetLastInsertedId()
    {
        $driver = $this->createDriver();
        $runner = QueryRunner::create($driver, $this->createQueryBuilder());

        $runner->run(insert('users', [

            'name' => 'New User',
            'username' => 'new',
            'password' => 'user',
            'created_at' => '2021-09-15 16:53:07',
            'updated_at' => '2021-09-22 16:53:12'

        ]));

        $lastInserted = $driver->getLastInsertedId();
        $lastFoundId = $runner->fetch(select(raw('MAX(id)'))->from('users'))->scalar();

        expect($lastFoundId)->toBe((int)$lastInserted);
    }

    public function testFetchByIntId()
    {
        $query = raw('SELECT id FROM users WHERE id = :id', [
            ':id' => [1, null]
        ]);
        $id = $this->createRunner()->fetch($query)->scalar();

        expect($id)->toBe(1);
    }

    public function testFetchByBoolId()
    {
        $query = raw('SELECT id FROM users WHERE id = :id', [
            ':id' => [true, null]
        ]);
        $id = $this->createRunner()->fetch($query)->scalar();

        expect($id)->toBe(1);
    }
}