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

namespace tests\Sql;

use ArekX\PQL\Sql\Query\Delete;
use ArekX\PQL\Sql\Query\Insert;
use ArekX\PQL\Sql\Query\Select;
use PHPUnit\Framework\TestCase;
use function ArekX\PQL\Sql\{delete, insert, select};

class FunctionsTest extends TestCase
{
    public function testSelect()
    {
        $q = select();
        expect($q)->toBeInstanceOf(Select::class);
        expect($q->get('columns'))->toBe(null);

        $q = select(['1', '2']);
        expect($q->get('columns'))->toBe(['1', '2']);
    }

    public function testInsert()
    {
        $q = insert('table', ['a' => 1]);
        expect($q)->toBeInstanceOf(Insert::class);
        expect($q->get('into'))->toBe('table');
        expect($q->get('columns'))->toBe(['a']);
        expect($q->get('values'))->toBe([[1]]);

        $q = insert('table');
        expect($q->get('into'))->toBe('table');
        expect($q->get('columns'))->toBe(null);
        expect($q->get('values'))->toBe(null);
    }

    public function testDelete()
    {
        $q = delete('table');
        expect($q)->toBeInstanceOf(Delete::class);
        expect($q->get('from'))->toBe('table');
        expect($q->get('where'))->toBe(null);

        $q = delete('table', ['all', ['id' => 1]]);
        expect($q->get('from'))->toBe('table');
        expect($q->get('where'))->toBe(['all', ['id' => 1]]);
    }
}