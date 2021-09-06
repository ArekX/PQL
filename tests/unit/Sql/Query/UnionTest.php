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

use ArekX\PQL\Sql\Query\Select;
use ArekX\PQL\Sql\Query\Union;

class UnionTest extends \Codeception\Test\Unit
{
    public function testCreation()
    {
        $u = Union::create();

        expect($u)->toBeInstanceOf(Union::class);
    }

    public function testFrom()
    {
        $q = Select::create();
        $u = Union::create()->from($q);

        expect($u->toArray()['from'])->toBe($q);
    }

    public function testUnion()
    {
        $q1 = Select::create();
        $q2 = Select::create();
        $u = Union::create()
            ->unionWith($q1)
            ->unionWith($q2);

        expect($u->toArray()['union'])->toBe([
            [$q1, null],
            [$q2, null]
        ]);
    }

    public function testUnionMixed()
    {
        $q1 = Select::create();
        $q2 = Select::create();
        $u = Union::create()
            ->unionWith($q1)
            ->unionWith($q2, 'all');

        expect($u->toArray()['union'])->toBe([
            [$q1, null],
            [$q2, 'all']
        ]);
    }
}