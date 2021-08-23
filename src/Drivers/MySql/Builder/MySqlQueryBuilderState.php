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
use ArekX\PQL\Sql\SqlParamBuilder;

/**
 * Represents a query builder state for MySQL
 */
class MySqlQueryBuilderState implements QueryBuilderState
{
    /**
     * State for the query build.
     *
     * @var array
     */
    protected $state = [];

    /**
     * Create new instance of this class
     *
     * @return static
     */
    public static function create()
    {
        return new static();
    }

    /**
     * Set currently used sql parameter builder.
     *
     * @param SqlParamBuilder $builder Main params builder to be used.
     */
    public function setParamsBuilder(SqlParamBuilder $builder): void
    {
        $this->set('paramsBuilder', $builder);
    }

    /**
     * @inheritDoc
     */
    public function set(string $name, $value): void
    {
        $this->state[$name] = $value;
    }

    /**
     * Sets the currently used main query builder.
     *
     * @param QueryBuilder $builder Main Query builder to be used.
     */
    public function setParentBuilder(QueryBuilder $builder): void
    {
        $this->set('parentBuilder', $builder);
    }

    /**
     * Return currently set sql parameter builder.
     *
     * @return SqlParamBuilder|null
     */
    public function getParamsBuilder(): ?SqlParamBuilder
    {
        return $this->get('paramsBuilder');
    }

    /**
     * @inheritDoc
     */
    public function get(string $name, $default = null)
    {
        if (!array_key_exists($name, $this->state)) {
            return $default;
        }
        return $this->state[$name];
    }

    /**
     * Return parent query builder to build sub queries.
     *
     * @return QueryBuilder|null
     */
    public function getParentBuilder(): ?QueryBuilder
    {
        return $this->get('parentBuilder');
    }

    /**
     * Set a glue for joining query parts in MySQL.
     *
     * @param string $glue Glue string to be used
     */
    public function setQueryPartGlue(string $glue)
    {
        $this->set('queryPartGlue', $glue);
    }

    /**
     * Return glue for joining query parts.
     *
     * @return string
     */
    public function getQueryPartGlue(): string
    {
        return $this->get('queryPartGlue', ' ');
    }
}