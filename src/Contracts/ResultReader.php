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
 * Interface for reading a result.
 */
interface ResultReader
{
    /**
     * Iterate results in the reader and returns them one by one.
     * @return \Generator
     */
    public function iterate(): \Generator;

    /**
     * Return whether there is a next value to be read.
     *
     * @return bool
     */
    public function hasNext(): bool;

    /**
     * Read and return the next value.
     *
     * If there is no next value, null is returned.
     *
     * @return mixed
     */
    public function read();
}