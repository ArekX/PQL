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

use ArekX\PQL\Drivers\Pdo\PdoDriver;
use ArekX\PQL\QueryRunner;
use PDO;
use function ArekX\PQL\Sql\all;
use function ArekX\PQL\Sql\insert;
use function ArekX\PQL\Sql\raw;
use function ArekX\PQL\Sql\select;

class SqlSrvDriverTest extends SqlSrvTestCase
{
    use \integration\Pdo\SecurityFixesTrait;

    public function fixtures(): array
    {
        return [
            'users' => __DIR__ . '/fixtures/users.php'
        ];
    }

    public function testPdoInstanceCreation()
    {
        $driver = $this->createDriver();

        expect($driver->getPdo())->toBeInstanceOf(PDO::class);

        $runner = QueryRunner::create($driver, $this->createQueryBuilder());
        $result = $runner->fetchFirst(select('*')->from('users'));

        $users = $this->getFixture('users');
        expect($result)->toBe($users['data'][0]);
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

        $driver->beginTransaction()->execute(function () use ($runner) {
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

        $driver->beginTransaction()->execute(function () {
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
                PdoDriver::STEP_BEFORE_RUN => [
                    function ($result, $params, $next) use (&$ranSteps) {
                        $ranSteps[] = PdoDriver::STEP_BEFORE_RUN;
                        expect($result->getQuery())->toBe('SELECT * FROM [users]');
                        return $next($result);
                    }
                ],
                PdoDriver::STEP_AFTER_RUN => [
                    function ($result, $params, $next) use (&$ranSteps) {
                        $ranSteps[] = PdoDriver::STEP_AFTER_RUN;
                        return $next($result);
                    }
                ],
            ]
        ]);

        $runner->fetchFirst(select('*')->from('users'));

        expect($ranSteps)->toBe([
            PdoDriver::STEP_BEFORE_RUN,
            PdoDriver::STEP_AFTER_RUN
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

        expect((int)$lastFoundId)->toBe((int)$lastInserted);
    }

    public function testFetchByIntId()
    {
        $query = raw('SELECT id FROM users WHERE id = :id', [
            ':id' => [1, null]
        ]);
        $id = $this->createRunner()->fetch($query)->scalar();

        expect($id)->toBe(1);
    }

    public function testTopLimit()
    {
        $query = select('*')->from('users')->orderBy(['id' => 'asc'])->limit(2);
        $rows = $this->createRunner()->fetchAll($query);

        $users = $this->getFixture('users');

        expect($rows)->toBe([$users['data'][0], $users['data'][1]]);
    }

    public function testOffsetFetch()
    {
        $query = select('*')->from('users')->orderBy(['id' => 'asc'])->offset(1)->limit(2);
        $rows = $this->createRunner()->fetchAll($query);

        $users = $this->getFixture('users');

        expect($rows)->toBe([$users['data'][1], $users['data'][2]]);
    }
}
