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

namespace ArekX\PQL;

use ArekX\PQL\Contracts\Driver;
use ArekX\PQL\Contracts\QueryBuilder;
use ArekX\PQL\Contracts\ResultReader;
use ArekX\PQL\Contracts\StructuredQueryRunner;
use ArekX\PQL\Contracts\ResultBuilder;
use ArekX\PQL\Contracts\StructuredQuery;

/**
 * Runner for queries for allowing easy use of the
 * driver and builder.
 */
class QueryRunner implements StructuredQueryRunner
{
    /**
     * Current driver in use.
     * @var Driver
     */
    public Driver $driver;

    /**
     * Current query builder in use.
     *
     * @var QueryBuilder
     */
    public QueryBuilder $builder;

    /**
     * Creates a new instance of this runner
     *
     * @param Driver|null $driver Driver to be set to be used
     * @param null $builder Builder to be used
     * @return static
     */
    public static function create($driver = null, $builder = null)
    {
        $instance = new static();

        if ($driver) {
            $instance->useDriver($driver);
        }

        if ($builder) {
            $instance->useBuilder($builder);
        }

        return $instance;
    }

    /**
     * @inheritDoc
     */
    public function useDriver(Driver $driver): StructuredQueryRunner
    {
        $this->driver = $driver;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function useBuilder(QueryBuilder $builder): StructuredQueryRunner
    {
        $this->builder = $builder;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function run(StructuredQuery $query)
    {
        return $this->driver->run($this->builder->build($query));
    }

    /**
     * @inheritDoc
     */
    public function fetchFirst(StructuredQuery $query)
    {
        return $this->driver->fetchFirst($this->builder->build($query));
    }

    /**
     * @inheritDoc
     */
    public function fetchAll(StructuredQuery $query): array
    {
        return $this->driver->fetchAll($this->builder->build($query));
    }

    /**
     * @inheritDoc
     */
    public function fetch(StructuredQuery $query): ResultBuilder
    {
        return $this->driver->fetch($this->builder->build($query));
    }

    /**
     * @inheritDoc
     */
    public function fetchReader(StructuredQuery $query): ResultReader
    {
        return $this->driver->fetchReader($this->builder->build($query));
    }
}