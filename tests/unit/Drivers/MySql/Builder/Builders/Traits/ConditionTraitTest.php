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
use Codeception\Test\Unit;
use tests\Drivers\MySql\Builder\Builders\Traits\mock\ConditionTester;

class ConditionTraitTest extends Unit
{
    public function testUnknownConditionType()
    {
        $this->expectException(\Exception::class);
        $this->assertCondition(['unknown-type', 'value']);
    }

    protected function assertCondition($condition, $query = '', $params = [])
    {
        $t = new ConditionTester();

        $state = $this->createState();
        $result = $t->build($condition, $state);

        expect($result)->toBe($query);
        expect($state->getParamsBuilder()->build())->toBe($params);
    }

    protected function createState()
    {
        /** @var MySqlQueryBuilderState $value */
        $value = (new MySqlQueryBuilder())->createState();
        return $value;
    }

    public function testColumnCondition()
    {
        $this->assertCondition(['column', 'active'], '`active`');
    }

    public function testColumnConditionWithNonString()
    {
        $this->expectException(\Exception::class);
        $this->assertCondition(['column', 1]);
    }

    public function testColumnConditionEmpty()
    {
        $this->expectException(\Exception::class);
        $this->assertCondition(['column']);
    }

    public function testBuildValue()
    {
        $this->assertCondition(['value', 'active'], ':t0', [
            ':t0' => ['active', null]
        ]);

        $this->assertCondition(['value', 'active', 'string'], ':t0', [
            ':t0' => ['active', 'string']
        ]);
    }

    public function testBuildAnd()
    {
        $this->assertCondition(['and', ['value', 1], ['value', 2] ], '(:t0) AND (:t1)', [
            ':t0' => [1, null],
            ':t1' => [2, null]
        ]);
    }

    public function testBuildOr()
    {
        $this->assertCondition(['or', ['value', 1], ['value', 2], ['column', 'col'] ], '(:t0) OR (:t1) OR (`col`)', [
            ':t0' => [1, null],
            ':t1' => [2, null]
        ]);
    }

    public function testBuildAllCondition()
    {
        $this->assertCondition(['all', [
            'is_active' => 1,
            'is_deleted' => 0
        ]], '`is_active` = :t0 AND `is_deleted` = :t1', [
            ':t0' => [1, null],
            ':t1' => [0, null]
        ]);
    }

    public function testBuildAnyCondition()
    {
        $this->assertCondition(['any', [
            'is_active' => 1,
            'is_deleted' => 0
        ]], '`is_active` = :t0 OR `is_deleted` = :t1', [
            ':t0' => [1, null],
            ':t1' => [0, null]
        ]);
    }

    public function testBuildEmptyValueIsNull()
    {
        $this->assertCondition(['value'], ':t0', [
            ':t0' => [null, null]
        ]);
    }
}