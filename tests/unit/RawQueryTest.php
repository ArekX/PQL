<?php
namespace tests;

use ArekX\PQL\RawQuery;

class RawQueryTest extends \Codeception\Test\Unit
{
    public function testCreation()
    {
        $r = RawQuery::create('query');

        expect($r)->toBeInstanceOf(RawQuery::class);
        expect($r->getQuery())->toBe('query');
        expect($r->getParams())->toBe(null);
        expect($r->getConfig())->toBe(null);
    }

    public function testPassingParams()
    {
        $r = RawQuery::create('query', [
            ':key' => 'value'
        ]);

        expect($r)->toBeInstanceOf(RawQuery::class);
        expect($r->getQuery())->toBe('query');
        expect($r->getParams())->toBe([':key' => 'value']);
        expect($r->getConfig())->toBe(null);
    }

    public function testPassingConfig()
    {
        $r = RawQuery::create('query', [
            ':key' => 'value'
        ], ['config' => 'value']);

        expect($r)->toBeInstanceOf(RawQuery::class);
        expect($r->getQuery())->toBe('query');
        expect($r->getParams())->toBe([':key' => 'value']);
        expect($r->getConfig())->toBe(['config' => 'value']);
    }
}