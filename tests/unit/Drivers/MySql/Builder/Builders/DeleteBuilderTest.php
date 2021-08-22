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

class DeleteBuilderTest extends \Codeception\Test\Unit
{
    public function testBuilder()
    {
        $builder = new MySqlQueryBuilder();

        $q = Delete::create()->from('table');

        expect($builder->build($q)->getQuery())->toBe('DELETE FROM table');
    }
}