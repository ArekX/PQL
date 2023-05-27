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

use ArekX\PQL\Contracts\GroupedSubQuery;
use ArekX\PQL\Contracts\StructuredQuery;
use ArekX\PQL\Query;
use ArekX\PQL\Sql\Query\Traits\ConditionTrait;
use ArekX\PQL\Sql\Query\Traits\ConfigureTrait;
use ArekX\PQL\Sql\Query\Traits\JoinTrait;

/**
 * Class representing a select query for retrieving data from
 * database.
 */
class Select extends Query implements GroupedSubQuery
{
    use ConditionTrait;
    use JoinTrait;
    use ConfigureTrait;

    /**
     * Sets a data source from which to pull the data from
     * such as a table of data, depending on the driver.
     *
     * **SQL Injection Warning**: Value in this function is not usually escaped in the driver
     * and should not be used to pass values from the user input to it. If you need to pass
     * and escape query use Raw query.
     *
     * If the source is passed as an array then the key of this source
     * is used as an alias if the driver supports it and values is used
     * as the source name.
     *
     * If null is passed it means that this query part is not to be used.
     *
     * @param string|array|StructuredQuery|null $source Source to be passed
     * @return $this
     */
    public function from($source): self
    {
        $this->use('from', $source);
        return $this;
    }

    /**
     * Set columns to be retrieved from data source
     *
     * **SQL Injection Warning**: Value in this function is not usually escaped in the driver
     * and should not be used to pass values from the user input to it. If you need to pass
     * and escape query use Raw query.
     *
     * If columns are passed as an array then the key of the array
     * is used as an alias if the driver supports it and values is used
     * as the column name.
     *
     * If null is passed it means that this query part is not to be used.
     *
     * @param array|string|StructuredQuery|null $columns
     * @return $this
     */
    public function columns($columns)
    {
        $this->use('columns', $columns);
        return $this;
    }

    /**
     * Add a column to the columns list.
     *
     * **SQL Injection Warning**: Value in this function is not usually escaped in the driver
     * and should not be used to pass values from the user input to it. If you need to pass
     * and escape query use Raw query.
     *
     * If passed column is an array it will be merged
     * with the current column list.
     *
     * @param array|string|StructuredQuery $column Column to be added
     * @return $this
     * @see Select::columns()
     */
    public function addColumns($column)
    {
        $columns = $this->get('columns');

        if (!is_array($columns)) {
            $columns = [$columns];
        }

        if (is_array($column)) {
            foreach ($column as $key => $value) {
                $columns[$key] = $value;
            }
        } else {
            $columns[] = $column;
        }

        $this->use('columns', $columns);
        return $this;
    }

    /**
     * Order results by a specific value.
     *
     * **SQL Injection Warning**: Value in this function is not usually escaped in the driver
     * and should not be used to pass values from the user input to it. If you need to pass
     * and escape query use Raw query.
     *
     * If the array is passed this function expects an associative array where key is the
     * column to sort by and value is how to sort by.
     *
     * If structured query is passed it is processed as is.
     *
     * @param array|StructuredQuery|null $orderBy
     * @return $this
     */
    public function orderBy($orderBy)
    {
        $this->use('orderBy', $orderBy);
        return $this;
    }

    /**
     * Group results by specified value.
     *
     * **SQL Injection Warning**: Value in this function is not usually escaped in the driver
     * and should not be used to pass values from the user input to it. If you need to pass
     * and escape query use Raw query.
     *
     * If array is passed here, every element of the array is used as the grouping column.
     *
     * If structured query is passed it is processed as is.
     *
     * @param array|StructuredQuery|null $groupBy
     */
    public function groupBy($groupBy)
    {
        $this->use('groupBy', $groupBy);
        return $this;
    }


    /**
     * Add a HAVING condition to the query.
     *
     * Condition logic is the same as with where method.
     *
     * @param array|StructuredQuery $having
     * @return $this
     * @see ConditionTrait::where()
     */
    public function having($having)
    {
        $this->use('having', $having);
        return $this;
    }

    /**
     * Append having to the existing one by AND-ing the previous having
     * filter to the value specified in having part.
     *
     * Condition accepts the same format as where method.
     *
     * If the current condition in part is an `['and', condition]`, new condition
     * will be appended to it, otherwise the previous condition and this condition will
     * be wrapped as `['and', previous, current]`
     *
     * @param array|StructuredQuery $having
     * @return $this
     * @see ConditionTrait::appendConditionPart()
     * @see ConditionTrait::where()
     */
    public function andHaving($having)
    {
        return $this->appendConditionPart('having', 'and', $having);
    }

    /**
     * Append having to the existing one by OR-ing the previous having
     * filter to the value specified in having part.
     *
     * Condition accepts the same format as where method.
     *
     * If the current condition in part is an `['or', condition]`, new condition
     * will be appended to it, otherwise the previous condition and this condition will
     * be wrapped as `['or', previous, current]`
     *
     * @param array|StructuredQuery $having
     * @return $this
     * @see ConditionTrait::appendConditionPart()
     * @see ConditionTrait::where()
     */
    public function orHaving($having)
    {
        return $this->appendConditionPart('having', 'or', $having);
    }

}
