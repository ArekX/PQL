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
use ArekX\PQL\Sql\Query\Traits\ConfigureTrait;

/**
 * Represents a query containing raw query or a value.
 */
class Raw extends Query
{
    use ConfigureTrait;

    /**
     * Create an instance of raw query from params and query.
     *
     * Query value can be a string or a different value
     * depending on the driver in use.
     *
     * @param mixed $query Driver dependent query
     * @param array|null $params Params to be bound to the query.
     * @return static
     */
    public static function from(mixed $query, array $params = null): static
    {
        return static::create()->query($query)->params($params);
    }

    /**
     * Set a query to be used.
     *
     * **SQL Injection Warning**: Value in this function is not usually escaped in the driver
     * and should not be used to pass values from the user input to it. If you need to
     * pass user value then define the parameters in this query and use params() function.
     *
     * @param mixed $query Driver dependent query.
     * @return $this
     */
    public function query(mixed $query): static
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
     * @param ?array $params
     * @return $this
     */
    public function params(?array $params): static
    {
        $this->use('params', $params);
        return $this;
    }
}
