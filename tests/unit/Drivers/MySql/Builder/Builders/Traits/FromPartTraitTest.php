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

use ArekX\PQL\Drivers\MySql\Builder\MySqlQueryBuilder;
use ArekX\PQL\Sql\Query\Delete;
use ArekX\PQL\Sql\Query\Raw;
use Codeception\Test\Unit;
use tests\Drivers\MySql\Builder\Builders\Traits\mock\FromPartTester;

class FromPartTraitTest extends Unit
{
    public function testFromWithString()
    {
        expect($this->build('table'))->toBe('FROM `table`');
    }

    protected function build($part)
    {
        $t = new FromPartTester();
        $builder = new MySqlQueryBuilder();
        return $t->build($part, $builder->createState());
    }

    public function testFromWithArray()
    {
        expect($this->build(['table1', 'asName' => 'table2']))->toBe('FROM `table1`, `table2` AS `asName`');
    }

    public function testFromWithStructuredQuery()
    {
        expect($this->build(Delete::create()->from('sub')))->toBe('FROM DELETE FROM `sub`');
    }

    public function testFromWithRaw()
    {
        expect($this->build(Raw::from("RAW QUERY")))->toBe('FROM RAW QUERY');
    }

    public function testFromWithStructuredQueryInAs()
    {
        expect($this->build([
            'alias1' => Delete::create()->from('sub')
        ]))->toBe('FROM DELETE FROM `sub` AS `alias1`');
    }

    public function testFromWithRawInAs()
    {
        $q = Raw::from("RAW QUERY");
        expect($this->build(['alias1' => $q]))->toBe('FROM RAW QUERY AS `alias1`');
    }
}