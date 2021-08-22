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

/**
 * Represents a high-level implementation for a
 * raw query  which can be used in a database driver.
 */
class RawQuery implements Contracts\RawQuery
{
    /**
     * Passed query.
     * @var mixed
     */
    protected $query = null;

    /**
     * Passed parameters
     * @var array|null
     */
    protected $params = null;

    /**
     * Passed configuration
     *
     * @var array|null
     */
    protected $config = null;

    /**
     * Create new instance of this class
     *
     * @param mixed $query Query to be used
     * @param array|null $params Parameters to be bound to the query
     * @param array|null $config Configuration to be bound to the query
     * @return static
     */
    public static function create($query, $params = null, $config = null)
    {
        return new static($query, $params, $config);
    }

    /**
     * Constructor for RawQuery
     *
     * @param mixed $query Query to be used
     * @param array|null $params Parameters to be bound to the query
     * @param array|null $config Configuration to be bound to the query
     */
    public function __construct($query, $params = null, $config = null)
    {
        $this->query = $query;
        $this->params = $params;
        $this->config = $config;
    }

    /**
     * @inheritDoc
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @inheritDoc
     */
    public function getParams(): ?array
    {
        return $this->params;
    }

    /**
     * @inheritDoc
     */
    public function getConfig(): ?array
    {
        return $this->config;
    }
}