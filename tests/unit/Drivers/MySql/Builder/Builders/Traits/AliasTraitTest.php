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
use ArekX\PQL\Drivers\MySql\Builder\MySqlQueryBuilderState;
use ArekX\PQL\Sql\Query\Delete;
use ArekX\PQL\Sql\Query\Raw;
use ArekX\PQL\Sql\SqlParamBuilder;
use Codeception\Test\Unit;
use tests\Drivers\MySql\Builder\Builders\Traits\mock\AliasTester;

class AliasTraitTest extends Unit
{
    public function testString()
    {
        $t = new AliasTester();

        expect($t->alias('Name'))->toBe('`Name`');
    }

    public function testArray()
    {
        $t = new AliasTester();

        expect($t->alias(['name1', 'name2']))->toBe('`name1`, `name2`');
    }

    public function testAliasTheSubQuery()
    {
        $t = new AliasTester();

        $builder = new MySqlQueryBuilder();
        expect($t->alias(Delete::create()->from('test'), $builder->createState()))->toBe('(DELETE FROM `test`)');
    }

    public function testAliasTheRawQuery()
    {
        $t = new AliasTester();

        $builder = new MySqlQueryBuilder();
        expect($t->alias(Raw::from('query'), $builder->createState()))->toBe('query');
    }

    public function testSubQueryInAlias()
    {
        $t = new AliasTester();

        $builder = new MySqlQueryBuilder();
        expect($t->alias(['alias1' => Delete::create()->from('test')], $builder->createState()))->toBe('(DELETE FROM `test`) AS `alias1`');
    }


    public function testRawInAlias()
    {
        $t = new AliasTester();

        $builder = new MySqlQueryBuilder();

        expect($t->alias(['alias1' => Raw::from('RAW QUERY')], $builder->createState()))->toBe('RAW QUERY AS `alias1`');
    }

    public function testArrayAs()
    {
        $t = new AliasTester();

        expect($t->alias(['as1' => 'name1', 'as2' => 'name2']))->toBe('`name1` AS `as1`, `name2` AS `as2`');
    }
}