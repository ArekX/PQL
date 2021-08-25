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
use ArekX\PQL\Sql\Statement\CaseWhen;
use tests\Sql\Query\QueryTestCase;

class CaseWhenTest extends QueryTestCase
{
    public function testCreation()
    {
        $q = CaseWhen::create();
        expect($q)->toBeInstanceOf(CaseWhen::class);
    }

    public function testOf()
    {
        $this->assertQueryPartValues(CaseWhen::create(), 'of', [
            ['value', 'val'],
            ['column', 'col'],
            Raw::create()
        ]);
    }

    public function testAdd()
    {
        $case1 = [['=', ['column', 'is_active'], ['value', 1]], ['value', 'active']];
        $case2 = [['=', ['column', 'is_active'], ['value', 0]], ['value', 'inactive']];
        $q = CaseWhen::create()
            ->addWhen(...$case1)
            ->addWhen(...$case2);

        expect($q->toArray()['when'])->toBe([
            $case1,
            $case2
        ]);
    }

    public function testWhen()
    {
        $case1 = [['=', ['column', 'is_active'], ['value', 1]], ['value', 'active']];
        $case2 = [['=', ['column', 'is_active'], ['value', 0]], ['value', 'inactive']];
        $q = CaseWhen::create()->when([
            $case1,
            $case2
        ]);

        expect($q->toArray()['when'])->toBe([
            $case1,
            $case2
        ]);
    }

    public function testElse()
    {
        $this->assertQueryPartValues(CaseWhen::create(), 'else', [
            ['value', 'Unknown'],
            Raw::create()
        ]);
    }
}