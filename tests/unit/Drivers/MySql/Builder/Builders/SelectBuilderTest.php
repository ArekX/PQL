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

use ArekX\PQL\Sql\Query\Delete;
use ArekX\PQL\Sql\Query\Insert;
use ArekX\PQL\Sql\Query\Raw;
use ArekX\PQL\Sql\Query\Select;
use ArekX\PQL\Sql\Query\Update;

class SelectBuilderTest extends BuilderTestCase
{
    public function testBuildColumns()
    {
        $this->assertQueryResults([
            [Select::create()->columns('*'), 'SELECT *'],
            [Select::create()->columns('1'), 'SELECT 1'],
            [Select::create()->columns('name'), 'SELECT `name`'],
            [Select::create()->columns(['alias' => 'name']), 'SELECT `name` AS `alias`'],
            [Select::create()->columns(Raw::from("RAW COLUMNS")), 'SELECT RAW COLUMNS'],
            [Select::create()->columns('name')->addColumns('value'), 'SELECT `name`, `value`'],
            [Select::create()->columns('name')->addColumns(['alias' => 'value']), 'SELECT `name`, `value` AS `alias`'],
        ]);
    }

    public function testBuildFrom()
    {
        $this->assertQueryResults([
            [Select::create()->columns('*')->from('table'), 'SELECT * FROM `table`'],
            [Select::create()->columns('*')->from(['alias' => 'table']), 'SELECT * FROM `table` AS `alias`'],
            [Select::create()->columns('*')->from(['alias' => 'table', 'table2']), 'SELECT * FROM `table` AS `alias`, `table2`'],
            [Select::create()->columns('*')->from(Raw::from('RAW1')), 'SELECT * FROM RAW1'],
        ]);
    }


    public function testBuildWhere()
    {
        $this->assertQueryResults([
            [Select::create()->columns('*')->from('table')->where(Raw::from('is_activated = 0')), 'SELECT * FROM `table` WHERE is_activated = 0'],
            [Select::create()->columns('*')->from('table')->where(['all', [
                'is_activated' => 1,
                'is_deleted' => 0
            ]]), 'SELECT * FROM `table` WHERE `is_activated` = :t0 AND `is_deleted` = :t1', [
                ':t0' => [1, null],
                ':t1' => [0, null],
            ]],
        ]);
    }

    public function testBuildLimit()
    {
        $this->assertQueryResults([
            [Select::create()->columns('*')->from('table')->limit(5), 'SELECT * FROM `table` LIMIT 5'],
        ]);
    }

    public function testBuildOffset()
    {
        $this->assertQueryResults([
            [Select::create()->columns('*')->from('table')->offset(10)->limit(5), 'SELECT * FROM `table` LIMIT 5 OFFSET 10'],
        ]);
    }

    public function testBuildHelperJoinTypes()
    {
        $this->assertQueryResults([
            [Select::create()->columns('*')->from('table')->leftJoin('table', 'table.id = t.id'), 'SELECT * FROM `table` LEFT JOIN `table` ON table.id = t.id'],
            [Select::create()->columns('*')->from('table')->rightJoin('table', 'table.id = t.id'), 'SELECT * FROM `table` RIGHT JOIN `table` ON table.id = t.id'],
            [Select::create()->columns('*')->from('table')->innerJoin('table', 'table.id = t.id'), 'SELECT * FROM `table` INNER JOIN `table` ON table.id = t.id'],
            [Select::create()->columns('*')->from('table')->fullOuterJoin('table', 'table.id = t.id'), 'SELECT * FROM `table` FULL OUTER JOIN `table` ON table.id = t.id'],
        ]);
    }

    public function testBuildJoin()
    {
        $this->assertQueryResults([
            [Select::create()->columns('*')->from('table')->join('inner', 'table', 'table.id = t.id'), 'SELECT * FROM `table` INNER JOIN `table` ON table.id = t.id'],
            [Select::create()->columns('*')->from('table')->join('inner', 'table'), 'SELECT * FROM `table` INNER JOIN `table`'],
            [Select::create()->columns('*')->from('table')->join('inner', 'table', Raw::from('TEST')), 'SELECT * FROM `table` INNER JOIN `table` ON TEST'],
            [Select::create()->columns('*')->from('table')->join('left', 'table', 'table.id = t.id'), 'SELECT * FROM `table` LEFT JOIN `table` ON table.id = t.id'],
            [Select::create()->columns('*')->from('table')->join('right', 'table', 'table.id = t.id'), 'SELECT * FROM `table` RIGHT JOIN `table` ON table.id = t.id'],
            [Select::create()->columns('*')->from('table')->join('right', ['alias' => 'table'], 'table.id = t.id'), 'SELECT * FROM `table` RIGHT JOIN `table` AS `alias` ON table.id = t.id'],
            [Select::create()->columns('*')->from('table')->join('inner', Raw::from('QUERY'), 'table.id = t.id'), 'SELECT * FROM `table` INNER JOIN QUERY ON table.id = t.id'],
            [Select::create()->columns('*')->from('table')->join('inner', 'table', ['all', ['table.id' => 'column']]), 'SELECT * FROM `table` INNER JOIN `table` ON `table`.`id` = :t0', [
                ':t0' => ['column', null],
            ]],
        ]);
    }

    public function testBuildGroupBy()
    {
        $this->assertQueryResults([
            [Select::create()->columns('*')->from('table')->groupBy(['a', 'b']), 'SELECT * FROM `table` GROUP BY `a`, `b`'],
            [Select::create()->columns('*')->from('table')->groupBy(Raw::from('RAW')), 'SELECT * FROM `table` GROUP BY RAW'],
        ]);
    }

    public function testBuildHaving()
    {
        $this->assertQueryResults([
            [Select::create()->columns('*')->from('table')->having(Raw::from('RAW')), 'SELECT * FROM `table` HAVING RAW'],
            [Select::create()->columns('*')->from('table')->having(['=', ['column', 'a'], ['value', 5]]), 'SELECT * FROM `table` HAVING `a` = :t0', [
                ':t0' => [5, null]
            ]],
        ]);
    }

    public function testBuildOrder()
    {
        $this->assertQueryResults([
            [Select::create()->columns('*')->from('table')->orderBy(Raw::from('RAW')), 'SELECT * FROM `table` ORDER BY RAW'],
            [Select::create()->columns('*')->from('table')->orderBy(['a' => 'asc', 'b' => 'desc']), 'SELECT * FROM `table` ORDER BY `a` ASC, `b` DESC'],
            [Select::create()->columns('*')->from('table')->orderBy(['a' => SORT_ASC, 'b' => SORT_DESC]), 'SELECT * FROM `table` ORDER BY `a` ASC, `b` DESC'],
        ]);
    }
}