<?php
/**
 * Created by Aleksandar Panic
 * Date: 2020-02-23
 * Time: 12:13
 * License: MIT
 */

namespace Query;

use ArekX\PQL\Query;
use PHPUnit\Framework\TestCase;

class QueryTest extends TestCase
{
    public function testQueryParams()
    {
        $query = Query::create();

        $query
            ->select('select')
            ->from('from')
            ->join('join')
            ->where('where')
            ->group('group')
            ->order('order');

        $this->assertEquals([
            'select' => 'select',
            'from' => 'from',
            'join' => 'join',
            'group' => 'group',
            'where' => 'where',
            'order' => 'order'
        ], $query->raw());
    }

    public function testEmpty()
    {
        $query = Query::create();

        $this->assertEquals([
            'select' => null,
            'from' => null,
            'join' => null,
            'group' => null,
            'where' => null,
            'order' => null
        ], $query->raw());
    }

    public function testCopy()
    {
        $query = Query::create();

        $query->select('test');

        $new = $query->copy();

        $new->select('test2');

        $this->assertEquals($new->raw()['select'], 'test2');
        $this->assertEquals($query->raw()['select'], 'test');
    }

    public function testFromRaw()
    {
        $query = Query::create();

        $query->select('test1');

        $raw = [
            'select' => 'select1',
            'from' => 'from2',
            'join' => 'join3',
            'group' => 'group4',
            'where' => 'where5',
            'order' => 'order6'
        ];

        $query->readRaw($raw);

        $this->assertEquals($raw, $query->raw());
    }
}