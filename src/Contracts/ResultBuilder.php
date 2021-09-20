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
     * @return array
     */
    public function all();

    /**
     * Return all rows from the result reader
     * with applied pipeline methods.
     *
     * All pipeline methods are applied in the same order they are added.
     *
     * @return mixed
     */
    public function result();

    /**
     * Return only the first result from result reader.
     *
     * @return mixed
     */
    public function first();

    /**
     * Returns the value of the specified column in the first result.
     *
     * If there is no result, returned value is null.
     *
     * @param int $index Index of the column to use.
     * @return mixed
     */
    public function scalar($index = 0);

    /**
     * Returns an array containing all value from the specified column.
     *
     * @param int $index Index of the column to use.
     * @return mixed
     */
    public function column($index = 0): array;

    /**
     * Returns whether there is an actual result to be returned.
     *
     * @return bool
     */
    public function exists(): bool;

    /**
     * Clear the currently set processing pipeline
     *
     * @see ResultBuilder::result()
     * @return $this
     */
    public function clearPipeline(): self;

    /**
     * Add an index by processor to the pipeline.
     *
     * Index by indexes all results by a specified column
     * in the result.
     *
     * In this case following:
     *
     * ```php
     * [
     *    ['name' => 'John', 'age' => 20],
     *    ['name' => 'Sally', 'age' => 25],
     *    ['name' => 'Sue', 'age' => 32],
     * ]
     * ```
     *
     * When index by column is `age` results in:
     * ```php
     * [
     *    20 => ['name' => 'John', 'age' => 20],
     *    25 => ['name' => 'Sally', 'age' => 25],
     *    32 => ['name' => 'Sue', 'age' => 32],
     * ]
     * ```
     *
     * @see ResultBuilder::result()
     * @param string $column Name of the column to index the results by
     * @return $this
     */
    public function pipeIndexBy(string $column): self;

    /**
     * Add conversion of the result set to the list in the pipeline.
     *
     * This will result in an associative array where key is set from
     * the row's column defined by $keyColumn and value is set from the column
     * defined by $valueColumn in the same row.
     *
     * @param string $keyColumn
     * @param string $valueColumn
     * @return $this
     */
    public function pipeListBy(string $keyColumn, string $valueColumn): self;

    /**
     * Add sorting to the pipeline processing.
     *
     * This sorting uses a function in the following signature:
     * ```php
     * function ($a, $b) {
     *    return // -1, 0, 1
     * }
     * ```
     *
     * WIth the result:
     * -1 -> $a < $b
     * 0 -> $a = $b
     * 1 -> $a > b
     *
     * @see ResultBuilder::result()
     * @param callable $sorter Sort func
     * @return $this
     */
    public function pipeSort(callable $sorter): self;

    /**
     * Add a mapper to pipeline processing.
     *
     * Mapper function needs to be in the following signature:
     * ```php
     * function ($row) {
     *   $result = $row;
     *   // Do something with $row
     *
     *    return $result;
     * }
     * ```
     *
     *
     * @see ResultBuilder::result()
     * @param callable $mapper Mapper function which will be applied.
     * @return $this
     */
    public function pipeMap(callable $mapper): self;

    /**
     * Add a reducer function which changes the result.
     *
     * Reducer function needs to be in the following signature:
     * ```php
     * function ($previousResult, $row) {
     *    // Do something with $previousResult and $row
     *    return $previousResult;
     * }
     * ```
     *
     * @param callable $reducer Reducer function which will be applied.
     * @param mixed $initialValue Start value which will be set at the first reducer iteration.
     * @return $this
     */
    public function pipeReduce(callable $reducer, $initialValue = null): self;

    /**
     * Add a filter function which filters the results.
     *
     * Reducer function needs to be in the following signature:
     * ```php
     * function ($row) {
     *    return true; // or false if the row should be removed.
     * }
     * ```
     *
     * @param callable $filter Filter function which will be used.
     * @return $this
     */
    public function pipeFilter(callable $filter): self;

}