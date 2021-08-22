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
 * Interface which implements the state
 * for query builder.
 */
interface QueryBuilderState
{
    /**
     * Return the value stored in the state by name.
     *
     * @param string $name Name of the state item to be returned.
     * @param mixed $default Default value to be returned of the item is not found.
     * @return mixed
     */
    public function get(string $name, $default = null);

    /**
     * Store the value in the state.
     *
     * @param string $name Name of the value
     * @param mixed $value Value to be stored
     */
    public function set(string $name, $value): void;
}