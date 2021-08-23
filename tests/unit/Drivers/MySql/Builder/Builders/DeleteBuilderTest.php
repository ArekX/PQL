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

namespace tests\Drivers\MySql\Builder\Builders;

use ArekX\PQL\Drivers\MySql\Builder\MySqlQueryBuilder;
use ArekX\PQL\Sql\Query\Delete;
use ArekX\PQL\Sql\Query\Raw;

class DeleteBuilderTest extends \Codeception\Test\Unit
{
    public function testFromWithString()
    {
        $result = $this->build(Delete::create()->from('table'));
        expect($result->getQuery())->toBe('DELETE FROM `table`');
    }

    protected function build(Delete $query)
    {
        $builder = new MySqlQueryBuilder();
        return $builder->build($query);
    }

    public function testFromWithArray()
    {
        $result = $this->build(Delete::create()->from(['table1', 'asName' => 'table2']));
        expect($result->getQuery())->toBe('DELETE FROM `table1`, `table2` AS `asName`');
    }

    public function testFromWithStructuredQuery()
    {
        $q = Delete::create()->from('sub');
        $result = $this->build(Delete::create()->from($q));
        expect($result->getQuery())->toBe('DELETE FROM (DELETE FROM `sub`)');
    }

    public function testFromWithRaw()
    {
        $q = Raw::from("RAW QUERY");
        $result = $this->build(Delete::create()->from($q));
        expect($result->getQuery())->toBe('DELETE FROM RAW QUERY');
    }

    public function testFromWithStructuredQueryInAs()
    {
        $q = Delete::create()->from('sub');
        $result = $this->build(Delete::create()->from(['alias1' => $q]));
        expect($result->getQuery())->toBe('DELETE FROM (DELETE FROM `sub`) AS `alias1`');
    }

    public function testFromWithRawInAs()
    {
        $q = Raw::from("RAW QUERY");
        $result = $this->build(Delete::create()->from(['alias1' => $q]));
        expect($result->getQuery())->toBe('DELETE FROM RAW QUERY AS `alias1`');
    }
}