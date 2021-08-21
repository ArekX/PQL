<?php

namespace tests\src\Sql\Query;

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
        expect($q->toArray()['values'])->toBe(['value1', 'value2']);
    }
}