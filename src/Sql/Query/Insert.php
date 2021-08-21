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

use ArekX\PQL\Contracts\StructuredQuery;
use ArekX\PQL\Query;

/**
 * Represents an insert query
 * for adding new data
 */
class Insert extends Query
{
    /**
     * Set the destination to insert data into.
     *
     * SQL Injection Warning: Value in this function is not usually escaped in the driver
     * and should not be used to pass values from the user input to it. If you need to pass
     * and escape query use Raw query.
     *
     * If a StructuredQuery is passed, it is parsed as is.
     *
     * @param string|StructuredQuery $into Table or other destination to insert data into.
     * @return $this
     */
    public function into($into)
    {
        $this->use('into', $into);
        return $this;
    }

    /**
     * Set columns to insert.
     *
     * SQL Injection Warning: Value in this function is not usually escaped in the driver
     * and should not be used to pass values from the user input to it. If you need to pass
     * and escape query use Raw query.
     *
     * Column count should match the value count if an array is passed.
     *
     * If a StructuredQuery is passed, it is parsed as is.
     *
     * @param array|StructuredQuery $columns Columns to set.
     * @return $this
     */
    public function columns($columns)
    {
        $this->use('columns', $columns);
        return $this;
    }

    /**
     * Set values to insert.
     *
     * Value count should match the column count if an array is passed.
     *
     * Values are properly escaped making this method suitable to
     * accept user input.
     *
     * If a StructuredQuery is passed, it is parsed as is.
     *
     * @param array|StructuredQuery $values
     * @return $this
     */
    public function values($values)
    {
        $this->use('values', $values);
        return $this;
    }

    /**
     * Set columns and values from an associative array.
     *
     * Columns are parsed with columns() method and
     * values are parsed with values() method.
     *
     * @param array $data Associative array containing keys and values to insert.
     * @return $this
     */
    public function data(array $data)
    {
        $this->columns(array_keys($data));
        return $this->values(array_values($data));
    }
}