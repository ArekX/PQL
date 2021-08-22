<?php

namespace tests\Drivers\MySql\Builder;

use ArekX\PQL\Drivers\MySql\Builder\MySqlQueryBuilder;
use ArekX\PQL\Drivers\MySql\Builder\MySqlQueryBuilderState;
use ArekX\PQL\Sql\SqlParamBuilder;

class MySqlQueryBuilderStateTest extends \Codeception\Test\Unit
{
    public function testCreation()
    {
        $s = MySqlQueryBuilderState::create();
        expect($s)->toBeInstanceOf(MySqlQueryBuilderState::class);
    }

    public function testSetting()
    {
        $s = MySqlQueryBuilderState::create();
        expect($s->get('key'))->toBe(null);

        $s->set('key', 'value');
        expect($s->get('key'))->toBe('value');


        $s->set('key2', true);
        expect($s->get('key2'))->toBe(true);
    }

    public function testSetWithOverride()
    {
        $s = MySqlQueryBuilderState::create();
        expect($s->get('key'))->toBe(null);

        $s->set('key', 'value');
        expect($s->get('key'))->toBe('value');


        $s->set('key', true);
        expect($s->get('key'))->toBe(true);
    }

    public function testGettingUnknownIsNull()
    {
        $s = MySqlQueryBuilderState::create();
        $s->set('key', 'value');
        expect($s->get('unknown'))->toBe(null);
    }

    public function testGettingDefault()
    {
        $s = MySqlQueryBuilderState::create();
        $s->set('key', 'value');
        expect($s->get('unknown', 'default value'))->toBe('default value');
    }

    public function testGettingParamsBuilder()
    {
        $s = MySqlQueryBuilderState::create();
        $paramsBuilder = new SqlParamBuilder();
        $s->set('paramsBuilder', $paramsBuilder);

        expect($s->getParamsBuilder())->toBe($paramsBuilder);
    }

    public function testGettingParentBuilder()
    {
        $s = MySqlQueryBuilderState::create();
        $parentBuilder = new MySqlQueryBuilder();
        $s->set('parentBuilder', $parentBuilder);

        expect($s->getParentBuilder())->toBe($parentBuilder);
    }

    public function testQueryPartGlue()
    {
        $s = MySqlQueryBuilderState::create();
        expect($s->getQueryPartGlue())->toBe(' ');
        $s->set('queryPartGlue', PHP_EOL);
        expect($s->getQueryPartGlue())->toBe(PHP_EOL);
    }
}