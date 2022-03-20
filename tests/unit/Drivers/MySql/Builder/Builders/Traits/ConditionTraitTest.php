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

namespace unit\Drivers\MySql\Builder\Builders\Traits;

use ArekX\PQL\Drivers\Pdo\MySql\MySqlQueryBuilder;
use ArekX\PQL\Drivers\Pdo\MySql\MySqlQueryBuilderState;
use ArekX\PQL\Sql\Query\Raw;
use Codeception\Test\Unit;
use unit\Drivers\MySql\Builder\Builders\Traits\mock\ConditionTester;

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

        $this->assertCondition(['all', [
            'is_active' => Raw::from('Query')
        ]], '`is_active` IN Query');

        $this->assertCondition(['all', [
            'is_active' => [1, 2, 3]
        ]], '`is_active` IN (:t0, :t1, :t2)', [
            ':t0' => [1, null],
            ':t1' => [2, null],
            ':t2' => [3, null],
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

        $this->assertCondition(['any', [
            'is_active' => Raw::from('Query')
        ]], '`is_active` IN Query');

        $this->assertCondition(['any', [
            'is_active' => [1, 2, 3]
        ]], '`is_active` IN (:t0, :t1, :t2)', [
            ':t0' => [1, null],
            ':t1' => [2, null],
            ':t2' => [3, null],
        ]);
    }

    public function testBuildEmptyValueIsNull()
    {
        $this->assertCondition(['value'], ':t0', [
            ':t0' => [null, null]
        ]);
    }

    public function testBuildNot()
    {
        $this->assertCondition(['not', ['all', ['is_active' => 1]]], 'NOT (`is_active` = :t0)', [
            ':t0' => [1, null]
        ]);

        $this->assertCondition(['not', Raw::from('QUERY')], 'NOT QUERY');
    }

    public function testBuildExists()
    {
        $this->assertCondition(['exists', ['all', ['is_active' => 1]]], 'EXISTS (`is_active` = :t0)', [
            ':t0' => [1, null]
        ]);

        $this->assertCondition(['exists', Raw::from('QUERY')], 'EXISTS QUERY');
    }

    public function testBuildBetween()
    {
        $this->assertCondition(['between', ['value', 1], ['value', -5], ['value', 22]], ':t0 BETWEEN :t1 AND :t2', [
            ':t0' => [1, null],
            ':t1' => [-5, null],
            ':t2' => [22, null]
        ]);

        $this->assertCondition(['between', Raw::from('DATE()'), ['value', -5], ['value', 22]], 'DATE() BETWEEN :t0 AND :t1', [
            ':t0' => [-5, null],
            ':t1' => [22, null]
        ]);

        $this->assertCondition(['between', ['value', 1], Raw::from('DATE()'), ['value', 22]], ':t0 BETWEEN DATE() AND :t1', [
            ':t0' => [1, null],
            ':t1' => [22, null]
        ]);

        $this->assertCondition(['between', ['value', 1], ['value', -5],Raw::from('DATE()')], ':t0 BETWEEN :t1 AND DATE()', [
            ':t0' => [1, null],
            ':t1' => [-5, null]
        ]);
    }

    public function testBuildIn()
    {
        $this->assertCondition(['in', ['column', 'id'], ['value', [1, 2, 3]]], '`id` IN (:t0, :t1, :t2)', [
            ':t0' => [1, null],
            ':t1' => [2, null],
            ':t2' => [3, null]
        ]);

        $this->assertCondition(['in', ['column', 'id'], Raw::from('QUERY')], '`id` IN QUERY');
    }

    public function testBuildLike()
    {
        $this->assertCondition(['like', ['column', 'name'], ['value', '%test%']], '`name` LIKE :t0', [
            ':t0' => ['%test%', null],
        ]);

        $this->assertCondition(['like', ['column', 'name'], Raw::from('QUERY')], '`name` LIKE QUERY');
    }

    public function testBuildEquals()
    {
        $this->assertCondition(['=', ['column', 'name'], ['value', 'test']], '`name` = :t0', [
            ':t0' => ['test', null],
        ]);

        $this->assertCondition(['=', ['column', 'name'], Raw::from('QUERY')], '`name` = QUERY');
    }

    public function testBuildGreaterThan()
    {
        $this->assertCondition(['>', ['column', 'times'], ['value', 5]], '`times` > :t0', [
            ':t0' => [5, null],
        ]);

        $this->assertCondition(['>', ['column', 'times'], Raw::from('QUERY')], '`times` > QUERY');
    }

    public function testBuildLessThan()
    {
        $this->assertCondition(['<', ['column', 'times'], ['value', 5]], '`times` < :t0', [
            ':t0' => [5, null],
        ]);

        $this->assertCondition(['<', ['column', 'times'], Raw::from('QUERY')], '`times` < QUERY');
    }

    public function testBuildGreaterEqualThan()
    {
        $this->assertCondition(['>=', ['column', 'times'], ['value', 5]], '`times` >= :t0', [
            ':t0' => [5, null],
        ]);

        $this->assertCondition(['>=', ['column', 'times'], Raw::from('QUERY')], '`times` >= QUERY');
    }

    public function testBuildLessEqualThan()
    {
        $this->assertCondition(['<=', ['column', 'times'], ['value', 5]], '`times` <= :t0', [
            ':t0' => [5, null],
        ]);

        $this->assertCondition(['<=', ['column', 'times'], Raw::from('QUERY')], '`times` <= QUERY');
    }

    public function testBuildNotEqual()
    {
        $this->assertCondition(['!=', ['column', 'times'], ['value', 5]], '`times` <> :t0', [
            ':t0' => [5, null],
        ]);

        $this->assertCondition(['!=', ['column', 'times'], Raw::from('QUERY')], '`times` <> QUERY');

        $this->assertCondition(['<>', ['column', 'times'], ['value', 5]], '`times` <> :t0', [
            ':t0' => [5, null],
        ]);

        $this->assertCondition(['<>', ['column', 'times'], Raw::from('QUERY')], '`times` <> QUERY');
    }
}