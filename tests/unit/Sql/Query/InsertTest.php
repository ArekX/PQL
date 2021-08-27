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
use ArekX\PQL\Sql\Query\Insert;
use ArekX\PQL\Sql\Query\Raw;

class InsertTest extends QueryTestCase
{
    public function testCreation()
    {
        $q = Insert::create();
        expect($q)->toBeInstanceOf(Insert::class);
    }

    public function testInto()
    {
        $this->assertQueryPartValues(Insert::create(), 'into', [
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

    public function testColumns()
    {
        $this->assertQueryPartValues(Insert::create(), 'columns', [
            ['column1', 'column2'],
            Raw::create()
        ]);
    }

    public function testValues()
    {
        $this->assertQueryPartValues(Insert::create(), 'columns', [
            ['value1', 'value2'],
            Raw::create()
        ]);
    }

    public function testData()
    {
        $q = Insert::create();

        $q->data([
            'column1' => 'value1',
            'column2' => 'value2'
        ]);
        expect($q->toArray()['columns'])->toBe(['column1', 'column2']);
        expect($q->toArray()['values'])->toBe([['value1', 'value2']]);
    }
}