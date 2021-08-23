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

namespace tests\Drivers\MySql\Builder\Builders\Traits;

use Codeception\Test\Unit;
use tests\Drivers\MySql\Builder\Builders\Traits\mock\QuoteTester;

class QuoteNameTraitTest extends Unit
{
    public function testQuotingUppercaseName()
    {
        $tester = new QuoteTester();

        expect($tester->quote('Item'))->toBe('`Item`');
    }

    public function testQuotingAName()
    {
        $tester = new QuoteTester();

        expect($tester->quote('item'))->toBe('`item`');
    }

    public function testQuotingNamesWithDots()
    {
        $tester = new QuoteTester();

        expect($tester->quote('item1.item_2.item3'))->toBe('`item1`.`item_2`.`item3`');
    }

    public function testDoNothingIfBackticksArePresent()
    {
        $tester = new QuoteTester();

        expect($tester->quote('item1.item2.`item3`'))->toBe('item1.item2.`item3`');
    }
}