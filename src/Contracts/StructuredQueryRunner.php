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

namespace ArekX\PQL\Contracts;

/**
 * Interface for running instances of structured query.
 */
interface StructuredQueryRunner
{
    /**
     * Set a driver to be used for queries.
     *
     * @param Driver $driver Driver to be used.
     * @return $this
     */
    public function useDriver(Driver $driver): self;

    /**
     * Set query builder to be used.
     *
     * @param QueryBuilder $builder Builder to be used.
     * @return $this
     */
    public function useBuilder(QueryBuilder $builder): self;

    /**
     * Execute a structured query and returns the result of the execution.
     *
     * This method is often to run queries for which you might only need affected
     * records.
     *
     * @param StructuredQuery $query Query to be ran.
     * @return mixed
     */
    public function run(StructuredQuery $query);

    /**
     * Execute a structured query and return only the first result.
     *
     * @param StructuredQuery $query Query to be run.
     * @return mixed
     */
    public function fetchFirst(StructuredQuery $query);

    /**
     * Execute a structured query and return all results.
     *
     * @param StructuredQuery $query Query to be run.
     * @return mixed
     */
    public function fetchAll(StructuredQuery $query): array;

    /**
     * Execute a structured query and return the result in a form of a reader.
     *
     * @param StructuredQuery $query Query to be run.
     * @return mixed
     */
    public function fetchReader(StructuredQuery $query): ResultReader;

    /**
     * Execute a structured query and return the result builder
     * so that the result can be formatted.
     *
     * @param StructuredQuery $query Query to be run.
     * @return mixed
     */
    public function fetch(StructuredQuery $query): ResultBuilder;
}
