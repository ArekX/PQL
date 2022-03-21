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

namespace unit\Drivers\MySql\Builder\Builders;

use ArekX\PQL\Drivers\Pdo\MySql\MySqlQueryBuilder;
use ArekX\PQL\Drivers\Pdo\MySql\MySqlQueryBuilderState;
use ArekX\PQL\Sql\Query\Delete;
use ArekX\PQL\Sql\Query\Raw;

class RawBuilderTest extends BuilderTestCase
{
    public function testRawQuery()
    {
        $this->assertQueryResults([
            [Raw::from("SELECT 1"), 'SELECT 1']
        ]);
    }

    public function testRawParams()
    {
        $result = $this->build(Raw::from("SELECT sssssss:val", [
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

    public function testRawParamsGetAppendedOnSameState()
    {
        $builder = new MySqlQueryBuilder();

        /** @var MySqlQueryBuilderState $state */
        $state = $builder->createState();
        $builder->build(Raw::from("SELECT :val1", [
            ':val1' => [1, 'string']
        ]), $state);
        $builder->build(Raw::from("SELECT :val2", [
            ':val2' => [2, 'string']
        ]), $state);

        expect($state->getParamsBuilder()->build())->toBe([
            ':val1' => [1, 'string'],
            ':val2' => [2, 'string'],
        ]);
    }
}