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

namespace tests\Sql;

use ArekX\PQL\Sql\Query\Select;
use ArekX\PQL\Sql\SqlQueryBuilderFactory;
use Codeception\Stub\Expected;
use mock\QueryBuilderMock;
use mock\QueryBuilderStateMock;

class SqlQueryBuilderFactoryTest extends \Codeception\Test\Unit
{
    public function testCreatingABuilder()
    {
        $mock = new QueryBuilderMock();

        $mock->query = 'Result query';
        $mock->params = [
            ':key' => 'Value'
        ];

        $state = new QueryBuilderStateMock();
        $s = $this->make(SqlQueryBuilderFactory::class, [
            'createBuilder' => fn() => $mock,
            'createState' => fn() => $state
        ]);

        $result = $s->build(Select::create());

        expect($result->getQuery())->toBe($mock->query);
        expect($result->getConfig())->toBe($mock->config);
        expect($result->getConfig())->toBe($mock->config);
    }


    public function testBuilderInstantiatedOnlyOnce()
    {
        $mock = new QueryBuilderMock();

        $mock->query = 'Result query';
        $mock->params = [
            ':key' => 'Value'
        ];

        $state = new QueryBuilderStateMock();
        $s = $this->make(SqlQueryBuilderFactory::class, [
            'createBuilder' => Expected::once($mock),
            'createState' => fn() => $state
        ]);

        $s->build(Select::create());
        $result = $s->build(Select::create());

        expect($result->getQuery())->toBe($mock->query);
        expect($result->getConfig())->toBe($mock->config);
        expect($result->getConfig())->toBe($mock->config);
    }

    public function testStateNotCreatedWhenOneIsPassed()
    {
        $mock = new QueryBuilderMock();

        $mock->query = 'Result query';
        $mock->params = [
            ':key' => 'Value'
        ];

        $s = $this->make(SqlQueryBuilderFactory::class, [
            'createBuilder' => Expected::once($mock),
            'createState' => Expected::never()
        ]);

        $state = new QueryBuilderStateMock();
        $result = $s->build(Select::create(), $state);

        expect($result->getQuery())->toBe($mock->query);
        expect($result->getConfig())->toBe($mock->config);
        expect($result->getConfig())->toBe($mock->config);
    }
}