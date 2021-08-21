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

namespace ArekX\PQL\Sql\Query;

use ArekX\PQL\Query;

/**
 * Represents a query containing raw query or a value.
 */
class Raw extends Query
{
    /**
     * Create an instance of raw query from params and query.
     *
     * @param mixed $query
     * @param array $params
     * @return Raw
     */
    public static function from($query, $params = null)
    {
        return static::create()->query($query)->params($params);
    }

    /**
     * Set a query to be used.
     *
     * SQL Injection Warning: Value in this function is not usually escaped in the driver
     * and should not be used to pass values from the user input to it. If you need to
     * pass user value then define the parameters in this query and use params() function.
     *
     * @param mixed $query
     * @return $this
     */
    public function query($query)
    {
        $this->use('query', $query);
        return $this;
    }

    /**
     * Define parameters in query to be used.
     *
     * These are the parameters to be used by this query.
     * They will be implemented based on the driver used.
     *
     * Usual use case for these parameters are SQL parameters
     * when running an SQL query.
     *
     * If null is passed, no params will be used.
     *
     * @param array|null $params
     * @return $this
     */
    public function params($params)
    {
        $this->use('params', $params);
        return $this;
    }
}