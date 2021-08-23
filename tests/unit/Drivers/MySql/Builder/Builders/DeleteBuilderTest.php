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
}