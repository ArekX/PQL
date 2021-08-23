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

class RawBuilderTest extends \Codeception\Test\Unit
{
    public function testRawQuery()
    {
        $result = $this->build(Raw::from("SELECT 1"));
        expect($result->getQuery())->toBe('SELECT 1');
    }

    public function testRawParams()
    {
        $result = $this->build(Raw::from("SELECT :val", [
            ':val' => 1
        ]));
        expect($result->getQuery())->toBe('SELECT :val');
        expect($result->getParams())->toBe([
            ':val' => [1, null]
        ]);
    }

    public function testRawParamsAsArray()
    {
        $result = $this->build(Raw::from("SELECT :val", [
            ':val' => [1, 'string']
        ]));
        expect($result->getQuery())->toBe('SELECT :val');
        expect($result->getParams())->toBe([
            ':val' => [1, 'string']
        ]);
    }

    protected function build(Raw $query)
    {
        $builder = new MySqlQueryBuilder();
        return $builder->build($query);
    }
}