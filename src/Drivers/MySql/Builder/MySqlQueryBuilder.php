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

namespace ArekX\PQL\Drivers\MySql\Builder;

use ArekX\PQL\Contracts\QueryBuilder;
use ArekX\PQL\Contracts\QueryBuilderState;
use ArekX\PQL\Drivers\MySql\Builder\Builders\DeleteBuilder;
use ArekX\PQL\Drivers\MySql\Builder\Builders\QueryPartBuilder;
use ArekX\PQL\Sql\Query\Delete;
use ArekX\PQL\Sql\SqlParamBuilder;
use ArekX\PQL\Sql\SqlQueryBuilderFactory;

/**
 * Represents a query builder for MySQL
 */
class MySqlQueryBuilder extends SqlQueryBuilderFactory
{
    const BUILDERS = [
        Delete::class => DeleteBuilder::class
    ];

    /**
     * @inheritDoc
     */
    protected function createBuilder(string $queryClass): QueryBuilder
    {
        if (empty(self::BUILDERS[$queryClass])) {
            throw new \Exception('No builder defined for: ' . $queryClass);
        }

        /** @var QueryPartBuilder $builderClass */
        $builderClass = self::BUILDERS[$queryClass];

        return new $builderClass();
    }

    /**
     * @inheritDoc
     */
    protected function createState(): QueryBuilderState
    {
        $state = MySqlQueryBuilderState::create();

        $state->set('paramsBuilder', SqlParamBuilder::create());
        $state->set('parentBuilder', $this);

        return $state;
    }
}