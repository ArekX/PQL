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

namespace unit\Drivers\Sqlite\Builder;

use ArekX\PQL\Drivers\Pdo\Common\Builders\DeleteBuilder;
use ArekX\PQL\Drivers\Pdo\Common\CommonQueryBuilderState;
use ArekX\PQL\Drivers\Pdo\Sqlite\SqliteQueryBuilder;
use ArekX\PQL\Sql\Query\Delete;
use ArekX\PQL\Sql\SqlParamBuilder;
use mock\QueryBuilderMock;
use mock\QueryMock;

class SqliteQueryBuilderTest extends \Codeception\Test\Unit
{
    public function testGettingABuilder()
    {
        $s = new SqliteQueryBuilder();

        expect($s->getBuilder(Delete::create()))->toBeInstanceOf(DeleteBuilder::class);
    }

    public function testUnknownQueryShouldThrowAnError()
    {
        $s = new SqliteQueryBuilder();

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

        $s = $this->make(SqliteQueryBuilder::class, [
            'createBuilder' => fn() => $mock
        ]);

        $s->build(Delete::create());

        /** @var CommonQueryBuilderState $state */
        $state = $mock->lastState;

        expect($state)->toBeInstanceOf(CommonQueryBuilderState::class);
        expect($state->getParentBuilder())->toBe($s);
        expect($state->getParamsBuilder())->toBeInstanceOf(SqlParamBuilder::class);
    }

    public function testStateIsConfiguredForSqliteDialect()
    {
        $state = (new SqliteQueryBuilder())->createState();

        expect($state->getQuoteCharacter())->toBe('"');
        expect($state->supportsModifyLimit())->toBe(false);
    }
}
