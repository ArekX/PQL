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

namespace tests;

use ArekX\PQL\Query;

class QueryTest extends \Codeception\Test\Unit
{
    public function testCreation()
    {
        $q = Query::create();

        expect($q)->toBeInstanceOf(Query::class);
        expect($q->toArray())->toBe([]);
    }

    public function testGet()
    {
        $q = Query::create()
            ->use('part1', 'value1')
            ->use('part2', 'value2')
            ->use('part3', 'value3');

        expect($q)->toBeInstanceOf(Query::class);
        expect($q->get('part1'))->toBe('value1');
        expect($q->get('part2'))->toBe('value2');
        expect($q->get('part3'))->toBe('value3');
    }

    public function testUse()
    {
        $q = Query::create()
            ->use('part1', 'value1')
            ->use('part2', 'value2');

        expect($q)->toBeInstanceOf(Query::class);
        expect($q->toArray())->toBe([
            'part1' => 'value1',
            'part2' => 'value2',
        ]);
    }

    public function testAppend()
    {
        $q = Query::create()
            ->append('part', 'value1')
            ->append('part', 'value2');

        expect($q)->toBeInstanceOf(Query::class);
        expect($q->toArray())->toBe([
            'part' => ['value1', 'value2'],
        ]);
    }


    public function testAppendAfterUse()
    {
        $q = Query::create()
            ->use('part', 'value0')
            ->append('part', 'value1')
            ->append('part', 'value2');

        expect($q)->toBeInstanceOf(Query::class);
        expect($q->toArray())->toBe([
            'part' => ['value0', 'value1', 'value2'],
        ]);
    }
}