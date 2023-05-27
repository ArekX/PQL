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

namespace ArekX\PQL\Sql;

use ArekX\PQL\Contracts\QueryBuilder;
use ArekX\PQL\Contracts\QueryBuilderFactory;
use ArekX\PQL\Contracts\QueryBuilderState;
use ArekX\PQL\Contracts\RawQuery;
use ArekX\PQL\Contracts\StructuredQuery;

/**
 * Represents a sql query builder factory
 * for mapping queries to builders.
 */
abstract class SqlQueryBuilderFactory implements QueryBuilderFactory, QueryBuilder
{
    /**
     * Created builder instances.
     * @var QueryBuilder[]
     */
    protected $createdBuilders = [];

    /**
     * @inheritDoc
     */
    public function build(StructuredQuery $query, QueryBuilderState $state = null): RawQuery
    {
        return $this->getBuilder($query)->build($query, $state ?: $this->createState());
    }

    /**
     * @inheritDoc
     */
    public function getBuilder(StructuredQuery $query): QueryBuilder
    {
        $class = get_class($query);

        if (empty($this->createdBuilders[$class])) {
            $this->createdBuilders[$class] = $this->createBuilder($class);
        }

        return $this->createdBuilders[$class];
    }

    /**
     * Create a builder from a query class.
     *
     * @param string $queryClass Class of the query to be created.
     * @return QueryBuilder
     */
    abstract protected function createBuilder(string $queryClass): QueryBuilder;

    /**
     * Create a new state for this query builder.
     * @return QueryBuilderState
     */
    abstract public function createState(): QueryBuilderState;
}
