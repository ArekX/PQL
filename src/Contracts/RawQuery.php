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
 * Interface or raw processed queries.
 */
interface RawQuery
{
    /**
     * Return the raw query
     *
     * Query format depends on the driver in use.
     *
     * @return mixed
     */
    public function getQuery(): mixed;

    /**
     * Return params to be bound to the query.
     *
     * Array returned should have the key as param name
     * and value as the value to be bound.
     *
     * Value depends on the driver in use.
     *
     * If null is returned, no params are in use.
     *
     * @return array|null
     */
    public function getParams(): ?array;

    /**
     * Return additional config to be set for this query.
     *
     * Configuration should be an associative array with key meaning the
     * driver dependent configuration name and value being the driver dependent value.
     *
     * If null is returned, no configuration is set.
     *
     * @return array|null
     */
    public function getConfig(): ?array;
}
