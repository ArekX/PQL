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

namespace ArekX\PQL\Sql;

use ArekX\PQL\Contracts\StructuredQuery;
use ArekX\PQL\Sql\Query\Delete;
use ArekX\PQL\Sql\Query\Insert;
use ArekX\PQL\Sql\Query\Select;

if (!function_exists('\ArekX\PQL\Sql\select')) {
    /**
     * Create a SELECT query.
     *
     * @param null|array|StructuredQuery $columns Columns to be selected
     * @return Select
     * @see Select
     */
    function select($columns = null): Select
    {
        return Select::create()->columns($columns);
    }
}

if (!function_exists('\ArekX\PQL\Sql\insert')) {
    /**
     * Create a INSERT query
     *
     * @param string|StructuredQuery $into Where to insert data to.
     * @param array $data Associative array of data to be inserted.
     * @return Insert
     */
    function insert($into, $data = null)
    {
        $query = Insert::create()->into($into);

        if ($data !== null) {
            $query->data($data);
        }

        return $query;
    }
}

if (!function_exists('\ArekX\PQL\Sql\delete')) {
    /**
     * Creates a DELETE query
     *
     * @param string|StructuredQuery $from From where to delete
     * @param null|array|StructuredQuery $condition Filter for deletion
     * @return Delete
     */
    function delete($from, $condition = null)
    {
        return Delete::create()->from($from)->where($condition);
    }
}