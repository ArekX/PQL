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
use ArekX\PQL\Sql\Query\Union;

class UnionBuilderTest extends BuilderTestCase
{
    public function testRequiredParameters()
    {
        $this->expectException(\Exception::class);
        $this->build(Union::create());
    }

    public function testBuildFromWithoutAddingMoreUnionsIsANoOp()
    {
        $this->assertQueryResults([
            [Union::create()->from(Raw::from('CROSS TABLE')), 'CROSS TABLE'],
            [Union::create()->from(Delete::create()->from('test')), 'DELETE FROM `test`'],
        ]);
    }

    public function testBuildWithUnion()
    {
        $this->assertQueryResults([
            [Union::create()->from(Raw::from('SELECT 1'))->unionWith(Raw::from('SELECT 2')), 'SELECT 1 UNION SELECT 2'],
            [Union::create()->from(Raw::from('SELECT 1'))->unionWith(Delete::create()->from('test')), 'SELECT 1 UNION DELETE FROM `test`'],
        ]);
    }


    public function testBuildWithUnionWithType()
    {
        $this->assertQueryResults([
            [Union::create()->from(Raw::from('SELECT 1'))->unionWith(Raw::from('SELECT 2'), 'all'), 'SELECT 1 UNION ALL SELECT 2'],
            [Union::create()->from(Raw::from('SELECT 1'))->unionWith(Delete::create()->from('test'), 'all'), 'SELECT 1 UNION ALL DELETE FROM `test`'],
            [Union::create()
                ->from(Raw::from('SELECT 1'))
                ->unionWith(Raw::from('ORDINARY'))
                ->unionWith(Raw::from('SELECT 2'), 'all'), 'SELECT 1 UNION ORDINARY UNION ALL SELECT 2'],
        ]);
    }
}