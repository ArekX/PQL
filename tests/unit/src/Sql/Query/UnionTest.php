<?php
namespace tests\src\Sql\Query;

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
            ->union($q1)
            ->union($q2);

        expect($u->toArray()['union'])->toBe([
            [$q1, 'default'],
            [$q2, 'default']
        ]);
    }

    public function testUnionMixed()
    {
        $q1 = Select::create();
        $q2 = Select::create();
        $u = Union::create()
            ->union($q1)
            ->union($q2, 'all');

        expect($u->toArray()['union'])->toBe([
            [$q1, 'default'],
            [$q2, 'all']
        ]);
    }
}