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
        $s->setParamsBuilder($paramsBuilder);

        expect($s->getParamsBuilder())->toBe($paramsBuilder);
    }

    public function testGettingParentBuilder()
    {
        $s = MySqlQueryBuilderState::create();
        $parentBuilder = new MySqlQueryBuilder();
        $s->setParentBuilder($parentBuilder);

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