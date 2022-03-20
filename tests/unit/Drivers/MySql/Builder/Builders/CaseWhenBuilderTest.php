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

use ArekX\PQL\Sql\Query\Raw;
use ArekX\PQL\Sql\Statement\CaseWhen;

class CaseWhenBuilderTest extends BuilderTestCase
{
    public function testBuild()
    {
        $this->assertQueryResults([
            [CaseWhen::create()->addWhen(['=', ['column', 'a'], ['value', 1]], ['value', 'b']), 'CASE WHEN `a` = :t0 THEN :t1 END', [
                ':t0' => [1, null],
                ':t1' => ['b', null]
            ]],
            [CaseWhen::create()->addWhen(Raw::from('RAW1'), Raw::from('RAW2')), 'CASE WHEN RAW1 THEN RAW2 END'],
            [
                CaseWhen::create()
                    ->addWhen(['=', ['column', 'a'], ['value', 1]], ['value', 'b'])
                    ->else(['value', 'c']),
                'CASE WHEN `a` = :t0 THEN :t1 ELSE :t2 END',
                [
                    ':t0' => [1, null],
                    ':t1' => ['b', null],
                    ':t2' => ['c', null]
                ]
            ],
            [
                CaseWhen::create()
                    ->addWhen(['=', ['column', 'a'], ['value', 1]], ['value', 'b'])
                    ->else(Raw::from('RAW')),
                'CASE WHEN `a` = :t0 THEN :t1 ELSE RAW END',
                [
                    ':t0' => [1, null],
                    ':t1' => ['b', null],
                ]
            ],
        ]);
    }

    public function testRequiredParts()
    {
        expect(function () {
            $this->build(CaseWhen::create());
        })->callableToThrow(\Exception::class);

        expect(function () {
            $this->build(CaseWhen::create()->addWhen(['value', 1], ['value', 'b']));
        })->callableNotToThrow();

    }
}