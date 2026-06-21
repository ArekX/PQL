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

namespace unit\Drivers\SqlSrv\Builder\Builders;

use ArekX\PQL\Sql\Query\Select;

class SqlSrvSelectBuilderTest extends BuilderTestCase
{
    public function testIdentifiersAreQuotedWithBrackets()
    {
        $this->assertQueryResults([
            [Select::create()->columns(['a', 'b', 'c']), 'SELECT [a], [b], [c]'],
            [Select::create()->columns('*'), 'SELECT *'],
            [Select::create()->columns('name'), 'SELECT [name]'],
            [Select::create()->columns(['alias' => 'name']), 'SELECT [name] AS [alias]'],
            [Select::create()->columns('*')->from('table'), 'SELECT * FROM [table]'],
            [Select::create()->columns('*')->from(['alias' => 'table']), 'SELECT * FROM [table] AS [alias]'],
            [Select::create()->columns('*')->from('schema.table'), 'SELECT * FROM [schema].[table]'],
        ]);
    }

    public function testWhereCondition()
    {
        $this->assertQueryResults([
            [Select::create()->columns('*')->from('table')->where(['all', [
                'is_active' => 1,
            ]]), 'SELECT * FROM [table] WHERE [is_active] = :t0', [
                ':t0' => [1, null],
            ]],
        ]);
    }

    public function testLimitOnlyUsesTop()
    {
        $this->assertQueryResults([
            [Select::create()->columns('*')->from('table')->limit(5), 'SELECT TOP (5) * FROM [table]'],
            [Select::create()->columns(['a', 'b'])->from('table')->limit(10), 'SELECT TOP (10) [a], [b] FROM [table]'],
        ]);
    }

    public function testOffsetUsesOffsetFetch()
    {
        $this->assertQueryResults([
            [
                Select::create()->columns('*')->from('table')->orderBy(['id' => 'asc'])->offset(20),
                'SELECT * FROM [table] ORDER BY [id] ASC OFFSET 20 ROWS'
            ],
            [
                Select::create()->columns('*')->from('table')->orderBy(['id' => 'asc'])->offset(20)->limit(10),
                'SELECT * FROM [table] ORDER BY [id] ASC OFFSET 20 ROWS FETCH NEXT 10 ROWS ONLY'
            ],
        ]);
    }
}
