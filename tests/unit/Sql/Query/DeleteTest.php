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


namespace tests\Sql\Query;

use ArekX\PQL\Sql\Query\Delete;
use ArekX\PQL\Sql\Query\Raw;

class DeleteTest extends QueryTestCase
{
    public function testCreation()
    {
        $q = Delete::create();
        expect($q)->toBeInstanceOf(Delete::class);
    }


    public function testFrom()
    {
        $this->assertQueryPartValues(Delete::create(), 'from', [
            'table',
            Raw::create()
        ]);
    }

    public function testConfigure()
    {
        $this->assertQueryPartValues(Delete::create(), 'config', [
            ['key' => 'value'],
            ['key2' => 'value2']
        ]);
    }

    public function testWhere()
    {
        $this->assertQueryPartValues(Delete::create(), 'where', [
            ['=', 'a', 'b'],
            Delete::create(),
            null
        ]);
    }

    public function testAndWhere()
    {
        $q = Delete::create();

        $q->andWhere(['=', 'a', 'b']);

        expect($q->toArray()['where'])->toBe(['=', 'a', 'b']);

        $q->andWhere(['>', 'a', 2]);
        expect($q->toArray()['where'])->toBe(['and', ['=', 'a', 'b'], ['>', 'a', 2]]);

        $q->andWhere(['<', 'a', 0]);
        expect($q->toArray()['where'])->toBe(['and', ['=', 'a', 'b'], ['>', 'a', 2], ['<', 'a', 0]]);
    }

    public function testOrWhere()
    {
        $q = Delete::create();

        $q->orWhere(['=', 'a', 'b']);

        expect($q->toArray()['where'])->toBe(['=', 'a', 'b']);

        $q->orWhere(['>', 'a', 2]);
        expect($q->toArray()['where'])->toBe(['or', ['=', 'a', 'b'], ['>', 'a', 2]]);

        $q->orWhere(['<', 'a', 0]);
        expect($q->toArray()['where'])->toBe(['or', ['=', 'a', 'b'], ['>', 'a', 2], ['<', 'a', 0]]);
    }

    public function testJoin()
    {
        $q = Delete::create();

        $q->join('inner', 'table', 'onCondition');
        expect($q->toArray()['join'])->toBe([
            ['inner', 'table', 'onCondition']
        ]);

        $q->join('left', 'table2', 'onCondition2');

        expect($q->toArray()['join'])->toBe([
            ['inner', 'table', 'onCondition'],
            ['left', 'table2', 'onCondition2']
        ]);
    }

    public function testInnerJoin()
    {
        $q = Delete::create();

        $q->innerJoin('table', 'onCondition');
        expect($q->toArray()['join'])->toBe([
            ['inner', 'table', 'onCondition']
        ]);
    }

    public function testLeftJoin()
    {
        $q = Delete::create();

        $q->leftJoin('table', 'onCondition');
        expect($q->toArray()['join'])->toBe([
            ['left', 'table', 'onCondition']
        ]);
    }

    public function testRightJoin()
    {
        $q = Delete::create();

        $q->rightJoin('table', 'onCondition');
        expect($q->toArray()['join'])->toBe([
            ['right', 'table', 'onCondition']
        ]);
    }

    public function testFullOuterJoin()
    {
        $q = Delete::create();

        $q->fullOuterJoin('table', 'onCondition');
        expect($q->toArray()['join'])->toBe([
            ['full outer', 'table', 'onCondition']
        ]);
    }
}