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

use ArekX\PQL\Drivers\MySql\Builder\MySqlQueryBuilder;
use ArekX\PQL\Drivers\MySql\Builder\MySqlQueryBuilderState;
use ArekX\PQL\Sql\Query\Raw;
use Codeception\Test\Unit;
use tests\Drivers\MySql\Builder\Builders\Traits\mock\JoinPartTester;

class JoinTraitTest extends Unit
{
    public function testSingleJoin()
    {
        $this->assertPart([
            ['inner', 'table', 'on']
        ], 'INNER JOIN `table` ON on');
    }

    public function testSingleJoinOnRaw()
    {
        $this->assertPart([
            ['inner', 'table', Raw::from('RAW')]
        ], 'INNER JOIN `table` ON RAW');
    }

    public function testJoinWithoutOn()
    {
        $this->assertPart([
            ['inner', 'table', null]
        ], 'INNER JOIN `table`');
    }

    public function testAliasJoin()
    {
        $this->assertPart([
            ['inner', ['alias' => 'table'], 'on']
        ], 'INNER JOIN `table` AS `alias` ON on');
    }



    public function testRawQueryTable()
    {
        $this->assertPart([
            ['inner', Raw::from('RAW'), 'on']
        ], 'INNER JOIN RAW ON on');
    }

    public function testSingleJoinWithCondition()
    {
        $this->assertPart([
            ['inner', 'table', ['all', ['active' => 1]]]
        ], 'INNER JOIN `table` ON `active` = :t0', [
            ':t0' => [1, null]
        ]);
    }

    public function testMultipleJoins()
    {
        $this->assertPart([
            ['inner', 'table1', 'on'],
            ['left', 'table2', 'on1'],
            ['right', 'table3', 'on2'],
        ], 'INNER JOIN `table1` ON on LEFT JOIN `table2` ON on1 RIGHT JOIN `table3` ON on2');
    }

    protected function assertPart($part, $expectedResult, $expectedParams = [])
    {
        $t = new JoinPartTester();
        $builder = new MySqlQueryBuilder();
        /** @var MySqlQueryBuilderState $state */
        $state = $builder->createState();
        expect($t->build($part, $state))->toBe($expectedResult);
        expect($state->getParamsBuilder()->build())->toBe($expectedParams);
    }
}