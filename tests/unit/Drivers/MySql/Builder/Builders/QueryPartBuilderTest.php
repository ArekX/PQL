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

use ArekX\PQL\Drivers\Pdo\MySql\Builders\QueryPartBuilder;
use ArekX\PQL\Drivers\Pdo\MySql\MySqlQueryBuilderState;
use ArekX\PQL\Query;
use ArekX\PQL\Sql\SqlParamBuilder;
use Codeception\Test\Unit;

class QueryPartBuilderTest extends Unit
{
    public function testInitialPartsBuild()
    {
        $builder = $this->createBuilder([
            'getInitialParts' => ['PART1', 'PART2']
        ]);

        $query = Query::create();
        $state = MySqlQueryBuilderState::create();

        $state->set('paramsBuilder', new SqlParamBuilder());

        $raw = $builder->build($query, $state);

        expect($raw->getQuery())->toBe('PART1 PART2');
    }

    protected function createBuilder(array $override = [])
    {
        return $this->make(QueryPartBuilder::class, $override + [
                'getInitialParts' => fn() => [],
                'getLastParts' => fn() => [],
                'getPartBuilders' => fn() => [],
                'getRequiredParts' => fn() => [],
            ]);
    }

    public function testPartsBuilder()
    {
        $builder = $this->createBuilder([
            'getInitialParts' => fn() => ['PARTX'],
            'getPartBuilders' => fn() => [
                'part' => fn() => 'Test Part'
            ]
        ]);

        $query = Query::create();

        $query->use('part', 'Value');

        $state = MySqlQueryBuilderState::create();

        $state->set('paramsBuilder', new SqlParamBuilder());

        $raw = $builder->build($query, $state);

        expect($raw->getQuery())->toBe('PARTX Test Part');
    }

    public function testPartIsNotRanIfQueryDoesntSupplyIt()
    {
        $builder = $this->createBuilder([
            'getInitialParts' => fn() => ['INITIAL'],
            'getPartBuilders' => fn() => [
                'part' => fn() => 'Value',
                'part2' => fn() => 'Part2',
            ]
        ]);

        $query = Query::create();
        $query->use('part2', 'Value');

        $state = MySqlQueryBuilderState::create();
        $state->set('paramsBuilder', new SqlParamBuilder());

        $raw = $builder->build($query, $state);

        expect($raw->getQuery())->toBe('INITIAL Part2');
    }

    public function testErrorThrownWhenThereIsNoState()
    {
        $builder = $this->createBuilder([
            'getInitialParts' => fn() => [],
            'getPartBuilders' => fn() => []
        ]);

        $query = Query::create();

        $this->expectException(\Exception::class);
        $builder->build($query);
    }

    public function testRequiredPartsNotSetThrowsAnError()
    {
        $builder = $this->createBuilder([
            'getInitialParts' => fn() => ['INITIAL'],
            'getPartBuilders' => fn() => [
                'part' => fn() => 'Value',
                'part2' => fn() => 'Part2',
            ],
            'getRequiredParts' => fn() => [
                'part2'
            ]
        ]);

        $query = Query::create();
        $query->use('part', 'Value');

        $state = MySqlQueryBuilderState::create();
        $state->set('paramsBuilder', new SqlParamBuilder());

        $this->expectException(\Exception::class);
        $builder->build($query, $state);
    }

}