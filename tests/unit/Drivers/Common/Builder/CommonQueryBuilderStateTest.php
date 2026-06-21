<?php

/**
 * Copyright 2026 Aleksandar Panic
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

namespace unit\Drivers\Common\Builder;

use ArekX\PQL\Drivers\Pdo\Common\CommonQueryBuilder;
use ArekX\PQL\Drivers\Pdo\Common\CommonQueryBuilderState;
use ArekX\PQL\Sql\SqlParamBuilder;

class CommonQueryBuilderStateTest extends \Codeception\Test\Unit
{
    public function testCreation()
    {
        $s = CommonQueryBuilderState::create();
        expect($s)->toBeInstanceOf(CommonQueryBuilderState::class);
    }

    public function testSetting()
    {
        $s = CommonQueryBuilderState::create();
        expect($s->get('key'))->toBe(null);

        $s->set('key', 'value');
        expect($s->get('key'))->toBe('value');


        $s->set('key2', true);
        expect($s->get('key2'))->toBe(true);
    }

    public function testSetWithOverride()
    {
        $s = CommonQueryBuilderState::create();
        expect($s->get('key'))->toBe(null);

        $s->set('key', 'value');
        expect($s->get('key'))->toBe('value');


        $s->set('key', true);
        expect($s->get('key'))->toBe(true);
    }

    public function testGettingUnknownIsNull()
    {
        $s = CommonQueryBuilderState::create();
        $s->set('key', 'value');
        expect($s->get('unknown'))->toBe(null);
    }

    public function testGettingDefault()
    {
        $s = CommonQueryBuilderState::create();
        $s->set('key', 'value');
        expect($s->get('unknown', 'default value'))->toBe('default value');
    }

    public function testGettingParamsBuilder()
    {
        $s = CommonQueryBuilderState::create();
        $paramsBuilder = new SqlParamBuilder();
        $s->setParamsBuilder($paramsBuilder);

        expect($s->getParamsBuilder())->toBe($paramsBuilder);
    }

    public function testGettingParentBuilder()
    {
        $s = CommonQueryBuilderState::create();
        $parentBuilder = new CommonQueryBuilder();
        $s->setParentBuilder($parentBuilder);

        expect($s->getParentBuilder())->toBe($parentBuilder);
    }

    public function testQueryPartGlue()
    {
        $s = CommonQueryBuilderState::create();
        expect($s->getQueryPartGlue())->toBe(' ');
        $s->setQueryPartGlue(PHP_EOL);
        expect($s->getQueryPartGlue())->toBe(PHP_EOL);
    }

    public function testQuoteCharacterDefaultsToDoubleQuote()
    {
        $s = CommonQueryBuilderState::create();
        expect($s->getQuoteCharacter())->toBe('"');
        $s->setQuoteCharacter('`');
        expect($s->getQuoteCharacter())->toBe('`');
    }

    public function testSupportsModifyLimitDefaultsToFalse()
    {
        $s = CommonQueryBuilderState::create();
        expect($s->supportsModifyLimit())->toBe(false);
        $s->setSupportsModifyLimit(true);
        expect($s->supportsModifyLimit())->toBe(true);
    }
}
