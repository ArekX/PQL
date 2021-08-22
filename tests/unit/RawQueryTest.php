<?php
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