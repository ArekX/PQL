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

namespace unit\Drivers\MySql\Builder;

use ArekX\PQL\Drivers\Pdo\MySql\Builders\DeleteBuilder;
use ArekX\PQL\Drivers\Pdo\MySql\MySqlQueryBuilder;
use ArekX\PQL\Drivers\Pdo\MySql\MySqlQueryBuilderState;
use ArekX\PQL\Sql\Query\Delete;
use ArekX\PQL\Sql\SqlParamBuilder;
use mock\QueryBuilderMock;
use mock\QueryMock;

class MySqlQueryBuilderTest extends \Codeception\Test\Unit
{
    public function testGettingABuilder()
    {
        $s = new MySqlQueryBuilder();

        expect($s->getBuilder(Delete::create()))->toBeInstanceOf(DeleteBuilder::class);
    }

    public function testUnknownQueryShouldThrowAnError()
    {
        $s = new MySqlQueryBuilder();

        $this->expectException(\Exception::class);
        expect($s->getBuilder(QueryMock::create()));
    }

    public function testCreateState()
    {
        $mock = new QueryBuilderMock();

        $mock->query = 'Result query';
        $mock->params = [
            ':key' => 'Value'
        ];

        $s = $this->make(MySqlQueryBuilder::class, [
            'createBuilder' => fn() => $mock
        ]);

        $s->build(Delete::create());

        /** @var MySqlQueryBuilderState $state */
        $state = $mock->lastState;

        expect($state)->toBeInstanceOf(MySqlQueryBuilderState::class);
        expect($state->getParentBuilder())->toBe($s);
        expect($state->getParamsBuilder())->toBeInstanceOf(SqlParamBuilder::class);
    }
}