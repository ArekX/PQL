<?php
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