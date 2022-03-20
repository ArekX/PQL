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

use ArekX\PQL\Sql\Query\Delete;
use ArekX\PQL\Sql\Query\Insert;
use ArekX\PQL\Sql\Query\Raw;
use ArekX\PQL\Sql\Query\Update;

class UpdateBuilderTest extends BuilderTestCase
{
    public function testRequiredParameters()
    {
        expect(function () {
            $this->build(Update::create());
        })->callableToThrow(\Exception::class);

        expect(function () {
            $this->build(Update::create()->to('test'));
        })->callableToThrow(\Exception::class);

        expect(function () {
            $this->build(Update::create()->to('test')->set(['a' => 2]));
        })->callableNotToThrow();
    }

    public function testBuild()
    {
        $this->assertQueryResults([
            [
                Update::create()
                    ->to('table')
                    ->set([
                        'a' => 1,
                        'b' => 2
                    ]),
                'UPDATE `table` SET `a` = :t0, `b` = :t1',
                [
                    ':t0' => [1, null],
                    ':t1' => [2, null],
                ]
            ],
            [
                Update::create()
                    ->to(Raw::from('RAWTABLE'))
                    ->set(Raw::from('RAW VALUES')),
                'UPDATE RAWTABLE SET RAW VALUES',
            ],
        ]);
    }

    public function testBuildWhere()
    {
        $this->assertQueryResults([
            [Update::create()->to('table')->set(['a' => 1])->where(Raw::from('is_activated = 0')), 'UPDATE `table` SET `a` = :t0 WHERE is_activated = 0', [
                ':t0' => [1, null]
            ]],
            [Update::create()->to('table')->set(['a' => 1])->where(['all', [
                'is_activated' => 1,
                'is_deleted' => 0
            ]]), 'UPDATE `table` SET `a` = :t0 WHERE `is_activated` = :t1 AND `is_deleted` = :t2', [
                ':t0' => [1, null],
                ':t1' => [1, null],
                ':t2' => [0, null],
            ]],
        ]);
    }

    public function testBuildLimit()
    {
        $this->assertQueryResults([
            [Update::create()->to('table')->set(['a' => 1])->limit(5), 'UPDATE `table` SET `a` = :t0 LIMIT 5', [
                ':t0' => [1, null]
            ]],
        ]);
    }

    public function testBuildOffset()
    {
        $this->assertQueryResults([
            [Update::create()->to('table')->set(['a' => 1])->offset(10)->limit(5), 'UPDATE `table` SET `a` = :t0 LIMIT 5 OFFSET 10', [
                ':t0' => [1, null]
            ]],
        ]);
    }

    public function testBuildHelperJoinTypes()
    {
        $params = [':t0' => [1, null]];
        $this->assertQueryResults([
            [Update::create()->to('table')->set(['a' => 1])->leftJoin('table', 'table.id = t.id'), 'UPDATE `table` LEFT JOIN `table` ON table.id = t.id SET `a` = :t0', $params],
            [Update::create()->to('table')->set(['a' => 1])->rightJoin('table', 'table.id = t.id'), 'UPDATE `table` RIGHT JOIN `table` ON table.id = t.id SET `a` = :t0', $params],
            [Update::create()->to('table')->set(['a' => 1])->innerJoin('table', 'table.id = t.id'), 'UPDATE `table` INNER JOIN `table` ON table.id = t.id SET `a` = :t0', $params],
            [Update::create()->to('table')->set(['a' => 1])->fullOuterJoin('table', 'table.id = t.id'), 'UPDATE `table` FULL OUTER JOIN `table` ON table.id = t.id SET `a` = :t0', $params],
        ]);
    }

    public function testBuildJoin()
    {
        $params = [':t0' => [1, null]];
        $this->assertQueryResults([
            [Update::create()->to('table')->set(['a' => 1])->join('inner', 'table', 'table.id = t.id'), 'UPDATE `table` INNER JOIN `table` ON table.id = t.id SET `a` = :t0', $params],
            [Update::create()->to('table')->set(['a' => 1])->join('inner', 'table'), 'UPDATE `table` INNER JOIN `table` SET `a` = :t0', $params],
            [Update::create()->to('table')->set(['a' => 1])->join('inner', 'table', Raw::from('TEST')), 'UPDATE `table` INNER JOIN `table` ON TEST SET `a` = :t0', $params],
            [Update::create()->to('table')->set(['a' => 1])->join('left', 'table', 'table.id = t.id'), 'UPDATE `table` LEFT JOIN `table` ON table.id = t.id SET `a` = :t0', $params],
            [Update::create()->to('table')->set(['a' => 1])->join('right', 'table', 'table.id = t.id'), 'UPDATE `table` RIGHT JOIN `table` ON table.id = t.id SET `a` = :t0', $params],
            [Update::create()->to('table')->set(['a' => 1])->join('right', ['alias' => 'table'], 'table.id = t.id'), 'UPDATE `table` RIGHT JOIN `table` AS `alias` ON table.id = t.id SET `a` = :t0', $params],
            [Update::create()->to('table')->set(['a' => 1])->join('inner', Raw::from('QUERY'), 'table.id = t.id'), 'UPDATE `table` INNER JOIN QUERY ON table.id = t.id SET `a` = :t0', $params],
            [Update::create()->to('table')->set(['a' => 1])->join('inner', 'table', ['all', ['table.id' => 'column']]), 'UPDATE `table` INNER JOIN `table` ON `table`.`id` = :t0 SET `a` = :t1', [
                ':t0' => ['column', null],
                ':t1' => [1, null],
            ]],
        ]);
    }
}