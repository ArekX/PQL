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

namespace unit\Drivers\MySql\Builder\Builders\Traits;

use ArekX\PQL\Drivers\Pdo\MySql\MySqlQueryBuilder;
use ArekX\PQL\Drivers\Pdo\MySql\MySqlQueryBuilderState;
use ArekX\PQL\Sql\Query\Raw;
use Codeception\Test\Unit;
use unit\Drivers\MySql\Builder\Builders\Traits\mock\NumberPartTester;
use unit\Drivers\MySql\Builder\Builders\Traits\mock\WherePartTester;

class NumberPartTraitTest extends Unit
{
    public function testNumberPartBuild()
    {
        $this->assertResult('LIMIT ', 5, 'LIMIT 5');
        $this->assertResult('OFFSET ', 5, 'OFFSET 5');
    }

    public function testLimitBuild()
    {
        $t = new NumberPartTester();
        expect($t->buildLimitValue(5))->toBe('LIMIT 5');
    }

    public function testOffsetBuild()
    {
        $t = new NumberPartTester();
        expect($t->buildOffsetValue(5))->toBe('OFFSET 5');
    }

    protected function assertResult($part, $number, $expected)
    {
        $t = new NumberPartTester();
        expect($t->build($part, $number))->toBe($expected);
    }
}