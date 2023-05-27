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
use ArekX\PQL\Sql\Statement\Method;

class MethodBuilderTest extends BuilderTestCase
{
    public function testRequiredParameters()
    {
        expect(function () {
            $this->build(Method::create());
        })->callableToThrow(\UnexpectedValueException::class);

        expect(function () {
            $this->build(Method::create()->name('name'));
        })->callableNotToThrow();
    }

    public function testBuild()
    {
        $this->assertQueryResults([
            [Method::create()->name('METHOD_NAME'), 'METHOD_NAME'],
            [Method::create()->name('METHOD_NAME')->params([['value', 'a'], ['value', 'b']]), 'METHOD_NAME(:t0, :t1)', [
                ':t0' => ['a', null],
                ':t1' => ['b', null]
            ]],
            [Method::create()->name('METHOD_NAME')->params([['column', 'table.a'], ['value', 'b']]), 'METHOD_NAME(`table`.`a`, :t0)', [
                ':t0' => ['b', null]
            ]],
            [Method::create()->name('METHOD_NAME')
                ->params([['column', 'table.a'], ['value', 'b']])->addParam(['value', 'c']), 'METHOD_NAME(`table`.`a`, :t0, :t1)', [
                ':t0' => ['b', null],
                ':t1' => ['c', null]
            ]],
            [Method::create()->name(Raw::from('RAW'))->params([['column', 'table.a'], ['value', 'b']]), 'RAW(`table`.`a`, :t0)', [
                ':t0' => ['b', null]
            ]],
            [Method::create()->name('METHOD_NAME')->params([['column', 'table.a'], Raw::from('RAW')]), 'METHOD_NAME(`table`.`a`, RAW)'],
            [Method::create()->name('METHOD_NAME')->params([['column', 'table.a'], Raw::from('RAW')]), 'METHOD_NAME(`table`.`a`, RAW)']
        ]);
    }

    public function testBuildInvalidType()
    {
        expect(function () {
            $this->build(Method::create()->name('name')->addParam(['invalid', 'value']));
        })->callableToThrow(\UnexpectedValueException::class);

    }
}