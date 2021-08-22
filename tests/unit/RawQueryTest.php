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

use ArekX\PQL\RawQueryResult;

class RawQueryTest extends \Codeception\Test\Unit
{
    public function testCreation()
    {
        $r = RawQueryResult::create('query');

        expect($r)->toBeInstanceOf(RawQueryResult::class);
        expect($r->getQuery())->toBe('query');
        expect($r->getParams())->toBe(null);
        expect($r->getConfig())->toBe(null);
    }

    public function testPassingParams()
    {
        $r = RawQueryResult::create('query', [
            ':key' => 'value'
        ]);

        expect($r)->toBeInstanceOf(RawQueryResult::class);
        expect($r->getQuery())->toBe('query');
        expect($r->getParams())->toBe([':key' => 'value']);
        expect($r->getConfig())->toBe(null);
    }

    public function testPassingConfig()
    {
        $r = RawQueryResult::create('query', [
            ':key' => 'value'
        ], ['config' => 'value']);

        expect($r)->toBeInstanceOf(RawQueryResult::class);
        expect($r->getQuery())->toBe('query');
        expect($r->getParams())->toBe([':key' => 'value']);
        expect($r->getConfig())->toBe(['config' => 'value']);
    }
}