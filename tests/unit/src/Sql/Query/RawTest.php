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

namespace tests\src\Sql\Query;

use ArekX\PQL\Sql\Query\Raw;
use Codeception\Test\Unit;

class RawTest extends Unit
{
    public function testCreation()
    {
        $query = Raw::create();
        expect($query)->toBeInstanceOf(Raw::class);
        $array = $query->toArray();

        expect($array)->toBe([]);
    }

    public function testQuery()
    {
        $query = Raw::create()->query('value');
        expect($query->toArray()['query'])->toBe('value');

        $query->query(['value' => 'value1']);
        expect($query->toArray()['query'])->toBe(['value' => 'value1']);

        $query->query(null);
        expect($query->toArray()['query'])->toBe(null);

    }

    public function testParams()
    {
        $query = Raw::create()->params([
            ':key' => 'value',
            ':key2' => 'value2',
        ]);
        expect($query->toArray()['params'])->toBe([
            ':key' => 'value',
            ':key2' => 'value2',
        ]);


        $query->params(null);
        expect($query->toArray()['params'])->toBe(null);
    }

    public function testFrom()
    {
        $query = Raw::from('query', [
            ':key' => 'value',
            ':key2' => 'value2',
        ]);
        expect($query->toArray())->toBe([
            'query' => 'query',
            'params' => [
                ':key' => 'value',
                ':key2' => 'value2',
            ]
        ]);
    }
}