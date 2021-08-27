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
use ArekX\PQL\Sql\Query\Raw;

class DeleteBuilderTest extends BuilderTestCase
{
    public function testBuildFrom()
    {
        $this->assertQueryResults([
            [Delete::create()->from(Raw::from('CROSS TABLE')), 'DELETE FROM CROSS TABLE'],
            [Delete::create()->from(Delete::create()->from('subtable')), 'DELETE FROM (DELETE FROM `subtable`)'],
            [Delete::create()->from('table'), 'DELETE FROM `table`'],
            [Delete::create()->from('schema.table'), 'DELETE FROM `schema`.`table`'],
            [Delete::create()->from(['schema.table1', 'table2']), 'DELETE FROM `schema`.`table1`, `table2`'],
            [Delete::create()->from(['a' => 'table1', 'schema.table2']), 'DELETE FROM `table1` AS `a`, `schema`.`table2`'],
            [Delete::create()->from(['a' => Raw::from('rawquery')]), 'DELETE FROM rawquery AS `a`'],
            [Delete::create()->from(['a' => Delete::create()->from('test')]), 'DELETE FROM (DELETE FROM `test`) AS `a`'],
        ]);
    }

    public function testExpectExceptionWhenStringIsPassed()
    {
        $this->expectException(\Exception::class);
        $this->assertQueryResults([
            [Delete::create()->from('table')->where('is_active = 1'), 'DELETE FROM `table` WHERE is_active = 1'],
        ]);
    }

    public function testBuildWhere()
    {
        $this->assertQueryResults([
            [Delete::create()->from('table')->where(Raw::from('is_activated = 0')), 'DELETE FROM `table` WHERE is_activated = 0'],
            [Delete::create()->from('table')->where(['all', [
                'is_activated' => 1,
                'is_deleted' => 0
            ]]), 'DELETE FROM `table` WHERE `is_activated` = :t0 AND `is_deleted` = :t1', [
                ':t0' => [1, null],
                ':t1' => [0, null],
            ]],
        ]);
    }

    public function testBuildLimit()
    {
        $this->assertQueryResults([
            [Delete::create()->from('table')->limit(5), 'DELETE FROM `table` LIMIT 5'],
        ]);
    }

    public function testBuildOffset()
    {
        $this->assertQueryResults([
            [Delete::create()->from('table')->offset(10)->limit(5), 'DELETE FROM `table` LIMIT 5 OFFSET 10'],
        ]);
    }


    public function testBuildHelperJoinTypes()
    {
        $this->assertQueryResults([
            [Delete::create()->from('table')->leftJoin('table', 'table.id = t.id'), 'DELETE FROM `table` LEFT JOIN `table` ON table.id = t.id'],
            [Delete::create()->from('table')->rightJoin('table', 'table.id = t.id'), 'DELETE FROM `table` RIGHT JOIN `table` ON table.id = t.id'],
            [Delete::create()->from('table')->innerJoin('table', 'table.id = t.id'), 'DELETE FROM `table` INNER JOIN `table` ON table.id = t.id'],
            [Delete::create()->from('table')->fullOuterJoin('table', 'table.id = t.id'), 'DELETE FROM `table` FULL OUTER JOIN `table` ON table.id = t.id'],
        ]);
    }

    public function testBuildJoin()
    {
        $this->assertQueryResults([
            [Delete::create()->from('table')->join('inner', 'table', 'table.id = t.id'), 'DELETE FROM `table` INNER JOIN `table` ON table.id = t.id'],
            [Delete::create()->from('table')->join('inner', 'table'), 'DELETE FROM `table` INNER JOIN `table`'],
            [Delete::create()->from('table')->join('inner', 'table', Raw::from('TEST')), 'DELETE FROM `table` INNER JOIN `table` ON TEST'],
            [Delete::create()->from('table')->join('left', 'table', 'table.id = t.id'), 'DELETE FROM `table` LEFT JOIN `table` ON table.id = t.id'],
            [Delete::create()->from('table')->join('right', 'table', 'table.id = t.id'), 'DELETE FROM `table` RIGHT JOIN `table` ON table.id = t.id'],
            [Delete::create()->from('table')->join('right', ['alias' => 'table'], 'table.id = t.id'), 'DELETE FROM `table` RIGHT JOIN `table` AS `alias` ON table.id = t.id'],
            [Delete::create()->from('table')->join('inner', Raw::from('QUERY'), 'table.id = t.id'), 'DELETE FROM `table` INNER JOIN QUERY ON table.id = t.id'],
            [Delete::create()->from('table')->join('inner', 'table', ['all', ['table.id' => 'column']]), 'DELETE FROM `table` INNER JOIN `table` ON `table`.`id` = :t0', [
                ':t0' => ['column', null]
            ]],
        ]);
    }
}