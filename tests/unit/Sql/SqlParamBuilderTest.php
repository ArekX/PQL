<?php

namespace tests\Sql;

use ArekX\PQL\Sql\SqlParamBuilder;

class SqlParamBuilderTest extends \Codeception\Test\Unit
{
    public function testWrapValue()
    {
        $p = new SqlParamBuilder();

        expect($p->wrapValue(2))->toBe(':t0');
        expect($p->get(':t0'))->toBe([2, null]);

        expect($p->wrapValue('test', 'string'))->toBe(':t1');
        expect($p->get(':t1'))->toBe(['test', 'string']);
    }

    public function testWrapValuePrefixChange()
    {
        $p = new SqlParamBuilder();

        $p->prefix = ':param_';

        expect($p->wrapValue(2))->toBe(':param_0');
        expect($p->get(':param_0'))->toBe([2, null]);

        expect($p->wrapValue(false, 'bool'))->toBe(':param_1');
        expect($p->get(':param_1'))->toBe([false, 'bool']);
    }

    public function testAddCustomParam()
    {
        $p = new SqlParamBuilder();

        $p->add(':param', 2);

        expect($p->get(':param'))->toBe([2, null]);

        $p->add(':param', 'string', 'type');
        expect($p->get(':param'))->toBe(['string', 'type']);
    }

    public function testGetNonExistentKey()
    {
        $p = new SqlParamBuilder();

        $p->add(':param', 2);

        expect($p->get(':unknownparam'))->toBe(null);
    }

    public function testBuild()
    {
        $p = new SqlParamBuilder();
        $p->wrapValue('custom value1');
        $p->wrapValue('custom value2', 'type1');
        $p->add(':custom_param', 2);
        $p->add(':custom_param2', true, 'bool');

        expect($p->build())->toBe([
            ':t0' => ['custom value1', null],
            ':t1' => ['custom value2', 'type1'],
            ':custom_param' => [2, null],
            ':custom_param2' => [true, 'bool'],
        ]);
    }

    public function testBuildWithAddOverride()
    {
        $p = new SqlParamBuilder();
        $p->wrapValue('custom value1');
        $p->add(':t0', true, 'bool');

        expect($p->build())->toBe([
            ':t0' => [true, 'bool'],
        ]);
    }

    public function testBuildWithCustomPrefix()
    {
        $p = new SqlParamBuilder();
        $p->prefix = ':prefix_';
        $p->wrapValue('custom value1');
        $p->wrapValue('custom value2', 'type');
        $p->wrapValue('custom value3');

        expect($p->build())->toBe([
            ':prefix_0' => ['custom value1', null],
            ':prefix_1' => ['custom value2', 'type'],
            ':prefix_2' => ['custom value3', null],
        ]);
    }
}