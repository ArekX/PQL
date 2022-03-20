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

namespace unit\Sql\Query;

use ArekX\PQL\Contracts\StructuredQuery;
use ArekX\PQL\Sql\Query\Select;

class SelectTest extends QueryTestCase
{
    public function testCreation()
    {
        $select = Select::create();
        expect($select)->toBeInstanceOf(Select::class);
        $array = $select->toArray();

        expect($array)->toBe([]);
    }

    public function testFrom()
    {
        $this->assertQueryPartValues(Select::create(), 'from', [
            'table',
            ['alias1' => 'table', 'alias2' => 'table'],
            Select::create(),
            null
        ]);
    }

    protected function assertQueryPartValues(StructuredQuery $query, string $part, array $values)
    {
        foreach ($values as $value) {
            $query->{$part}($value);
            expect($query->toArray()[$part])->toBe($value);
        }
    }

    public function testColumns()
    {
        $this->assertQueryPartValues(Select::create(), 'columns', [
            '1',
            ['a', 'b' => 2, 'c' => 3],
            Select::create(),
            null
        ]);
    }

    public function testAddColumn()
    {
        $q = Select::create()
            ->columns('a');

        $q->addColumns('b');

        expect($q->toArray()['columns'])->toBe([
            'a',
            'b'
        ]);
    }

    public function testAddColumnWithAlias()
    {
        $q = Select::create()
            ->columns('a');

        $q->addColumns(['alias' => 'b']);

        expect($q->toArray()['columns'])->toBe([
            'a',
            'alias' => 'b'
        ]);
    }


    public function testOrderBy()
    {
        $this->assertQueryPartValues(Select::create(), 'orderBy', [
            ['column' => 'asc'],
            Select::create(),
            null
        ]);
    }

    public function testGroupBy()
    {
        $this->assertQueryPartValues(Select::create(), 'groupBy', [
            ['a', 'b', 'c'],
            Select::create(),
            null
        ]);
    }

    public function testWhere()
    {
        $this->assertQueryPartValues(Select::create(), 'where', [
            ['=', 'a', 'b'],
            Select::create(),
            null
        ]);
    }

    public function testAndWhere()
    {
        $q = Select::create();

        $q->andWhere(['=', 'a', 'b']);

        expect($q->toArray()['where'])->toBe(['=', 'a', 'b']);

        $q->andWhere(['>', 'a', 2]);
        expect($q->toArray()['where'])->toBe(['and', ['=', 'a', 'b'], ['>', 'a', 2]]);

        $q->andWhere(['<', 'a', 0]);
        expect($q->toArray()['where'])->toBe(['and', ['=', 'a', 'b'], ['>', 'a', 2], ['<', 'a', 0]]);
    }

    public function testOrWhere()
    {
        $q = Select::create();

        $q->orWhere(['=', 'a', 'b']);

        expect($q->toArray()['where'])->toBe(['=', 'a', 'b']);

        $q->orWhere(['>', 'a', 2]);
        expect($q->toArray()['where'])->toBe(['or', ['=', 'a', 'b'], ['>', 'a', 2]]);

        $q->orWhere(['<', 'a', 0]);
        expect($q->toArray()['where'])->toBe(['or', ['=', 'a', 'b'], ['>', 'a', 2], ['<', 'a', 0]]);
    }

    public function testHaving()
    {
        $q = Select::create();

        $q->having(['=', 'a', 'b']);

        expect($q->toArray()['having'])->toBe(['=', 'a', 'b']);
    }

    public function testAndHaving()
    {
        $q = Select::create();

        $q->andHaving(['=', 'a', 'b']);

        expect($q->toArray()['having'])->toBe(['=', 'a', 'b']);

        $q->andHaving(['>', 'a', 2]);
        expect($q->toArray()['having'])->toBe(['and', ['=', 'a', 'b'], ['>', 'a', 2]]);

        $q->andHaving(['<', 'a', 0]);
        expect($q->toArray()['having'])->toBe(['and', ['=', 'a', 'b'], ['>', 'a', 2], ['<', 'a', 0]]);
    }

    public function testOrHaving()
    {
        $q = Select::create();

        $q->orHaving(['=', 'a', 'b']);

        expect($q->toArray()['having'])->toBe(['=', 'a', 'b']);

        $q->orHaving(['>', 'a', 2]);
        expect($q->toArray()['having'])->toBe(['or', ['=', 'a', 'b'], ['>', 'a', 2]]);

        $q->orHaving(['<', 'a', 0]);
        expect($q->toArray()['having'])->toBe(['or', ['=', 'a', 'b'], ['>', 'a', 2], ['<', 'a', 0]]);
    }

    public function testLimit()
    {
        $q = Select::create();

        $q->limit(10);

        expect($q->toArray()['limit'])->toBe(10);
    }

    public function testOffset()
    {
        $q = Select::create();

        $q->offset(10);

        expect($q->toArray()['offset'])->toBe(10);
    }

    public function testJoin()
    {
        $q = Select::create();

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
        $q = Select::create();

        $q->innerJoin('table', 'onCondition');
        expect($q->toArray()['join'])->toBe([
            ['inner', 'table', 'onCondition']
        ]);
    }

    public function testLeftJoin()
    {
        $q = Select::create();

        $q->leftJoin('table', 'onCondition');
        expect($q->toArray()['join'])->toBe([
            ['left', 'table', 'onCondition']
        ]);
    }

    public function testRightJoin()
    {
        $q = Select::create();

        $q->rightJoin('table', 'onCondition');
        expect($q->toArray()['join'])->toBe([
            ['right', 'table', 'onCondition']
        ]);
    }

    public function testFullOuterJoin()
    {
        $q = Select::create();

        $q->fullOuterJoin('table', 'onCondition');
        expect($q->toArray()['join'])->toBe([
            ['full outer', 'table', 'onCondition']
        ]);
    }
}