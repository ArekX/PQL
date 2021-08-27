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

use ArekX\PQL\Sql\Query\Insert;
use ArekX\PQL\Sql\Query\Raw;

class InsertBuilderTest extends BuilderTestCase
{
    public function testRequiredParameters()
    {
        expect(function () {
            $this->build(Insert::create());
        })->callableToThrow(\Exception::class);

        expect(function () {
            $this->build(Insert::create()->into('test'));
        })->callableToThrow(\Exception::class);

        expect(function () {
            $this->build(Insert::create()->into('test')->columns(['a', 'b', 'c']));
        })->callableToThrow(\Exception::class);

        expect(function () {
            $this->build(Insert::create()->into('test')->columns(['a', 'b', 'c'])->values([1, 2, 3]));
        })->callableNotToThrow();
    }

    public function testBuild()
    {
        $this->assertQueryResults([
            [
                Insert::create()
                    ->into('table')
                    ->columns(['a', 'b', 'c'])
                    ->values([1, 2, 3]),
                'INSERT INTO `table` (`a`, `b`, `c`) VALUES (:t0, :t1, :t2)',
                [
                    ':t0' => [1, null],
                    ':t1' => [2, null],
                    ':t2' => [3, null],
                ]
            ],
            [
                Insert::create()
                    ->into(Raw::from('RAW1'))
                    ->columns(Raw::from('RAW2'))
                    ->values(Raw::from('RAW3')),
                'INSERT INTO RAW1 RAW2 VALUES RAW3',
            ],
        ]);
    }

    public function testBuildOnlyValues()
    {
        $this->assertQueryResults([
            [
                Insert::create()
                    ->into('table')
                    ->values([1, 2, 3]),
                'INSERT INTO `table` VALUES (:t0, :t1, :t2)',
                [
                    ':t0' => [1, null],
                    ':t1' => [2, null],
                    ':t2' => [3, null],
                ]
            ],
        ]);
    }

    public function testBuildEmptyColumns()
    {
        $this->assertQueryResults([
            [
                Insert::create()
                    ->into('table')
                    ->columns([])
                    ->values([1, 2, 3]),
                'INSERT INTO `table` VALUES (:t0, :t1, :t2)',
                [
                    ':t0' => [1, null],
                    ':t1' => [2, null],
                    ':t2' => [3, null],
                ]
            ],
        ]);
    }

    public function testBuildData()
    {
        $this->assertQueryResults([
            [
                Insert::create()
                    ->into('table')
                    ->data([
                        'a1' => 5,
                        'b2' => 'test',
                        'c2' => true
                    ]),
                'INSERT INTO `table` (`a1`, `b2`, `c2`) VALUES (:t0, :t1, :t2)',
                [
                    ':t0' => [5, null],
                    ':t1' => ['test', null],
                    ':t2' => [true, null],
                ]
            ],
        ]);
    }
}