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

namespace unit\Sql;

use ArekX\PQL\Sql\Query\Delete;
use ArekX\PQL\Sql\Query\Insert;
use ArekX\PQL\Sql\Query\Raw;
use ArekX\PQL\Sql\Query\Select;
use ArekX\PQL\Sql\Query\Union;
use ArekX\PQL\Sql\Query\Update;
use ArekX\PQL\Sql\Statement\Call;
use ArekX\PQL\Sql\Statement\CaseWhen;
use ArekX\PQL\Sql\Statement\Method;
use PHPUnit\Framework\TestCase;
use function ArekX\PQL\Sql\{all,
    andWith,
    any,
    asFilters,
    between,
    call,
    caseWhen,
    column,
    compare,
    delete,
    equal,
    exists,
    insert,
    method,
    not,
    orWith,
    raw,
    search,
    select,
    union,
    update,
    value
};

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

    public function testUpdate()
    {
        $q = update('table', ['a' => 1]);
        expect($q)->toBeInstanceOf(Update::class);
        expect($q->get('to'))->toBe('table');
        expect($q->get('set'))->toBe(['a' => 1]);

        $q = update('table', ['a' => 2], ['all', ['id' => 1]]);
        expect($q->get('to'))->toBe('table');
        expect($q->get('set'))->toBe(['a' => 2]);
        expect($q->get('where'))->toBe(['all', ['id' => 1]]);
    }

    public function testUnion()
    {
        $s1 = select('1');
        $s2 = select('2');
        $s3 = select('3');
        $q = union($s1, $s2, $s3);
        expect($q)->toBeInstanceOf(Union::class);
        expect($q->get('from'))->toBe($s1);
        expect($q->get('union'))->toBe([
            [$s2, null],
            [$s3, null]
        ]);

        $q = union($s1);
        expect($q->get('from'))->toBe($s1);
        expect($q->get('union'))->toBe(null);

        $q = union();
        expect($q->get('from'))->toBe(null);
        expect($q->get('union'))->toBe(null);
    }

    public function testRaw()
    {
        $q = raw('RAW', ['a' => 1]);
        expect($q)->toBeInstanceOf(Raw::class);
        expect($q->get('query'))->toBe('RAW');
        expect($q->get('params'))->toBe([
            'a' => 1
        ]);
    }

    public function testCall()
    {
        $q = call('PROCEDURE', ['value', 'a']);
        expect($q)->toBeInstanceOf(Call::class);
        expect($q->get('name'))->toBe('PROCEDURE');
        expect($q->get('params'))->toBe([
            ['value', 'a']
        ]);
    }

    public function testMethod()
    {
        $q = method('SUM', ['value', 'a']);
        expect($q)->toBeInstanceOf(Method::class);
        expect($q->get('name'))->toBe('SUM');
        expect($q->get('params'))->toBe([
            ['value', 'a']
        ]);
    }

    public function testCaseWhen()
    {
        $q = caseWhen(['column', 'a'], [['value', 1], ['value', 2]]);
        expect($q)->toBeInstanceOf(CaseWhen::class);
        expect($q->get('of'))->toBe(['column', 'a']);
        expect($q->get('when'))->toBe([
            [['value', 1], ['value', 2]]
        ]);

        $q = caseWhen();

        expect($q->get('of'))->toBe(null);
        expect($q->get('when'))->toBe([]);
    }

    public function testValue()
    {
        expect(value('a'))->toBe(['value', 'a']);
    }

    public function testColumn()
    {
        expect(column('a'))->toBe(['column', 'a']);
    }

    public function testAny()
    {
        expect(any(['a' => 1]))->toBe(['any', ['a' => 1]]);
    }

    public function testAll()
    {
        expect(all(['a' => 1]))->toBe(['all', ['a' => 1]]);
    }


    public function testNot()
    {
        expect(not(any(['a' => 1])))->toBe(['not', ['any', ['a' => 1]]]);
    }

    public function testExits()
    {
        expect(exists(any(['a' => 1])))->toBe(['exists', ['any', ['a' => 1]]]);
    }

    public function testAndWith()
    {
        expect(andWith(
            any(['a' => 1]),
            all(['b' => 1])
        ))->toBe([
            'and',
            ['any', ['a' => 1]],
            ['all', ['b' => 1]]
        ]);
    }


    public function testOrWith()
    {
        expect(orWith(
            any(['a' => 1]),
            all(['b' => 1])
        ))->toBe([
            'or',
            ['any', ['a' => 1]],
            ['all', ['b' => 1]]
        ]);
    }

    public function testCompare()
    {
        expect(compare(
            ['value', 1],
            '>',
            ['value', 2]
        ))->toBe([
            '>',
            ['value', 1],
            ['value', 2]
        ]);
    }

    public function testEqual()
    {
        expect(equal(
            ['value', 1],
            ['value', 2]
        ))->toBe([
            '=',
            ['value', 1],
            ['value', 2]
        ]);
    }

    public function testSearch()
    {
        expect(search(
            ['value', 1],
            'value'
        ))->toBe([
            'like',
            ['value', 1],
            ['value', '%value%']
        ]);
    }

    public function testBetween()
    {
        expect(between(
            ['value', 1.5],
            ['value', 1],
            ['value', 2]
        ))->toBe([
            'between',
            ['value', 1.5],
            ['value', 1],
            ['value', 2]
        ]);
    }

    public function testAsFilters()
    {
        expect(asFilters([
            [1, equal(value('a'), value('b'))],
            ['', equal(value('c'), value('d'))],
            [null, equal(value('e'), value('f'))],
        ]))->toBe([
            'and', ['=', ['value', 'a'], ['value', 'b']]
        ]);

        expect(asFilters([
            [1, equal(value('a'), value('b'))],
            ['', equal(value('c'), value('d'))],
            [fn() => false, equal(value('e'), value('f'))],
            [fn() => true, equal(value('h'), value('g'))],
        ]))->toBe([
            'and',
            ['=', ['value', 'a'], ['value', 'b']],
            ['=', ['value', 'h'], ['value', 'g']]
        ]);


        expect(asFilters([
            [1, equal(value('a'), value('b'))],
            ['', equal(value('c'), value('d'))],
            [fn() => false, equal(value('e'), value('f'))],
            [fn() => true, equal(value('h'), value('g'))],
        ], 'or'))->toBe([
            'or',
            ['=', ['value', 'a'], ['value', 'b']],
            ['=', ['value', 'h'], ['value', 'g']]
        ]);
    }
}