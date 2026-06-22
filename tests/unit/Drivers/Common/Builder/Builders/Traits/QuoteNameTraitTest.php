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

namespace unit\Drivers\Common\Builder\Builders\Traits;

use ArekX\PQL\Drivers\Pdo\Common\CommonQueryBuilderState;
use Codeception\Test\Unit;
use unit\Drivers\Common\Builder\Builders\Traits\mock\QuoteTester;

class QuoteNameTraitTest extends Unit
{
    public function testQuotingUppercaseName()
    {
        $tester = new QuoteTester();

        expect($tester->quote('Item'))->toBe('"Item"');
    }

    public function testQuoteCharacterIsTakenFromState()
    {
        $tester = new QuoteTester();

        $state = CommonQueryBuilderState::create();
        $state->setQuoteCharacter('`');

        expect($tester->quote('item1.item2', $state))->toBe('`item1`.`item2`');
    }

    public function testQuotingAName()
    {
        $tester = new QuoteTester();

        expect($tester->quote('item'))->toBe('"item"');
    }

    public function testQuotingNamesWithDots()
    {
        $tester = new QuoteTester();

        expect($tester->quote('item1.item_2.item3'))->toBe('"item1"."item_2"."item3"');
    }

    public function testEscapesClosingQuoteByDoubling()
    {
        $tester = new QuoteTester();

        // A double quote inside a default-dialect identifier is escaped, not passed through.
        expect($tester->quote('it"em'))->toBe('"it""em"');
    }

    public function testEscapesDialectClosingQuoteByDoubling()
    {
        $tester = new QuoteTester();

        $state = CommonQueryBuilderState::create();
        $state->setQuoteCharacter('`');

        expect($tester->quote('it`em', $state))->toBe('`it``em`');
    }

    public function testEscapesAsymmetricClosingQuoteByDoubling()
    {
        $tester = new QuoteTester();

        // SQL Server style brackets: only the closing bracket needs escaping.
        $state = CommonQueryBuilderState::create();
        $state->setQuoteCharacter('[', ']');

        expect($tester->quote('a]b', $state))->toBe('[a]]b]');
    }

    public function testPreservesStar()
    {
        $tester = new QuoteTester();

        expect($tester->quote('*'))->toBe('*');
        expect($tester->quote('table.*'))->toBe('"table".*');
    }

    public function testTrimsSurroundingWhitespacePerSegment()
    {
        $tester = new QuoteTester();

        expect($tester->quote(' item1 . item2 '))->toBe('"item1"."item2"');
    }

    public function testInjectionPayloadBecomesInertIdentifier()
    {
        $tester = new QuoteTester();

        // Structural tokens cannot survive: the whole thing is one quoted identifier.
        expect($tester->quote('id) OR (1=1'))->toBe('"id) OR (1=1"');
    }

    public function testRejectsEmptySegment()
    {
        $tester = new QuoteTester();

        $this->expectException(\InvalidArgumentException::class);
        $tester->quote('item1..item2');
    }

    public function testRejectsControlCharacters()
    {
        $tester = new QuoteTester();

        $this->expectException(\InvalidArgumentException::class);
        $tester->quote("item\x00");
    }
}
