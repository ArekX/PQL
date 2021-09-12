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

namespace tests\Drivers\MySql\Builder\Builders\Traits;

use ArekX\PQL\Drivers\Pdo\MySql\MySqlQueryBuilder;
use ArekX\PQL\Drivers\Pdo\MySql\MySqlQueryBuilderState;
use ArekX\PQL\Sql\Query\Raw;
use Codeception\Test\Unit;
use tests\Drivers\MySql\Builder\Builders\Traits\mock\WherePartTester;

class WhereTraitTest extends Unit
{
    public function testWhereStringWithCondition()
    {
        $this->assertPart(['all', [
            'is_active' => 1,
            'item' => true
        ]], 'WHERE `is_active` = :t0 AND `item` = :t1', [
            ':t0' => [1, null],
            ':t1' => [true, null]
        ]);
    }

    public function testWhereStringWithRawQuery()
    {
        $this->assertPart(Raw::from('QUERY'), 'WHERE QUERY', []);
    }

    protected function assertPart($part, $expectedResult, $expectedParams = [])
    {
        $t = new WherePartTester();
        $builder = new MySqlQueryBuilder();
        /** @var MySqlQueryBuilderState $state */
        $state = $builder->createState();
        expect($t->build($part, $state))->toBe($expectedResult);
        expect($state->getParamsBuilder()->build())->toBe($expectedParams);
    }
}