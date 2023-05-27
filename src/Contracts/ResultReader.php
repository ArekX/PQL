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
 * Interface for reading results.
 */
interface ResultReader
{
    /**
     * Resets the result reader so that it can be used from the start.
     */
    public function reset(): void;

    /**
     * Returns all rows in the result set.
     * @return array
     */
    public function getAllRows(): array;

    /**
     * Results an array of all columns.
     *
     * @param int $columnIndex Index of the column to return.
     * @return array
     */
    public function getAllColumns($columnIndex = 0): array;

    /**
     * Returns next row or null if there is no row to be returned.
     * @return mixed
     */
    public function getNextRow();

    /**
     * Returns next column value.
     * @param int $columnIndex Index of the column to return.
     * @return mixed
     */
    public function getNextColumn($columnIndex = 0);

    /**
     * Finalizes result reader
     */
    public function finalize(): void;

}
