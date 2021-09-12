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
 * Interface representing a result builder
 * for handling results from a driver.
 */
interface ResultBuilder
{
    /**
     * Set result reader to use.
     *
     * This result reader will be used to read data from.
     *
     * @param ResultReader $reader Reader to be used
     * @return $this
     */
    public function useReader(ResultReader $reader): self;

    /**
     * Return all rows from the result reader.
     *
     * All pipelines added to the result builder
     * will be processed before the result is returned.
     *
     * @return array
     */
    public function all(): array;

    /**
     * Return only the first result from result reader.
     *
     * All pipelines added to the result builder
     * will be processed before the result is returned.
     *
     * @return mixed
     */
    public function first();

    public function scalar($index = 0);

    public function column($index = 0);

    public function exists(): bool;

    public function list($keyColumn, $valueColumn);

    public function clearPipeline(): self;

    public function pipeIndexBy(string $column): self;

    public function pipeSort(callable $sorter): self;

    public function pipeMap(callable $mapper): self;

    public function pipeReduce(callable $reducer): self;

    public function pipeFilter(callable $filter): self;

}