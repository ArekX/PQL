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

use ArekX\PQL\Contracts\StructuredQuery;
use ArekX\PQL\Drivers\MySql\Builder\MySqlQueryBuilder;
use Codeception\Test\Unit;

class BuilderTestCase extends Unit
{
    protected function assertQueryResults(array $queries, MySqlQueryBuilder $state = null)
    {
        foreach ($queries as $query) {
            [$query, $expectedResult, $params] = $query + [
                2 => null
            ];
            $raw = $this->build($query, $state);
            expect($raw->getQuery())->toBe($expectedResult);

            if ($params !== null) {
                expect($raw->getParams())->toBe($params);
            }
        }
    }

    protected function build(StructuredQuery $query, MySqlQueryBuilder $state = null)
    {
        $builder = new MySqlQueryBuilder();
        return $builder->build($query, $state);
    }
}