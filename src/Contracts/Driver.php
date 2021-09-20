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
 * Interface for a driver to run the raw queries.
 */
interface Driver
{
    /**
     * Execute a query
     *
     * This function is used when result from the query is not needed
     * or the result needed is affected row count.
     *
     * @param RawQuery $query Raw query to run.
     * @return mixed
     */
    public function run(RawQuery $query);

    /**
     * Set a list of middlewares to be used.
     *
     * @param string $step Driver specific step on which the middleware can be used.
     * @param array $middlewareList List of middlewares to apply.
     * @return $this
     */
    public function useMiddleware(string $step, array $middlewareList): self;

    /**
     * Add a middleware to processing list.
     *
     * @param string $step Driver specific step on which the middleware can be used.
     * @param callable $middleware Middleware function to be applied.
     * @return $this
     */
    public function appendMiddleware(string $step, callable $middleware): self;

    /**
     * Return the first result from the query.
     *
     * @param RawQuery $query Raw query to be used.
     * @return mixed
     */
    public function fetchFirst(RawQuery $query);

    /**
     * Fetch result set as a reader.
     *
     * @param RawQuery $query Raw query to be used.
     * @return ResultReader
     */
    public function fetchReader(RawQuery $query): ResultReader;

    /**
     * Fetch all results.
     *
     * @param RawQuery $query Raw query to be used.
     * @return array
     */
    public function fetchAll(RawQuery $query): array;

    /**
     * Fetch a result set and return a result builder for
     * transforming the result.
     *
     * @param RawQuery $query Raw query to be used.
     * @return ResultBuilder
     * @see ResultBuilder
     */
    public function fetch(RawQuery $query): ResultBuilder;
}