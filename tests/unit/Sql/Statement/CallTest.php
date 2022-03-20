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

namespace unit\Sql\Statement;

use ArekX\PQL\Sql\Query\Raw;
use ArekX\PQL\Sql\Statement\Call;
use unit\Sql\Query\QueryTestCase;

class CallTest extends QueryTestCase
{
    public function testCreation()
    {
        $q = Call::create();
        expect($q)->toBeInstanceOf(Call::class);
    }

    public function testAs()
    {
        $q = Call::as('Procedure', ['value', 'param1'], ['value', 'param2']);
        expect($q)->toBeInstanceOf(Call::class);
        expect($q->toArray())->toBe([
            'name' => 'Procedure',
            'params' => [
                ['value', 'param1'],
                ['value', 'param2'],
            ]
        ]);
    }

    public function testName()
    {
        $this->assertQueryPartValues(Call::create(), 'name', [
            'name',
            Raw::create()
        ]);
    }


    public function testParams()
    {
        $this->assertQueryPartValues(Call::create(), 'params', [
            [ ['value', 'param'], ['column', 'param'] ],
            Raw::create()
        ]);
    }

    public function testAddParam()
    {
        $q = Call::create()
                ->params([['value', 'paramx']])
                ->addParam(['value', 'param1'])
                ->addParam(['value', 'param2'])
                ->addParam(['value', 'param3']);

        expect($q->toArray()['params'])->toBe([
            ['value', 'paramx'],
            ['value', 'param1'],
            ['value', 'param2'],
            ['value', 'param3'],
        ]);
    }
}