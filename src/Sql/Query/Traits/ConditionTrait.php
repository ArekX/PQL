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

namespace ArekX\PQL\Sql\Query\Traits;

use ArekX\PQL\Contracts\StructuredQuery;

trait ConditionTrait
{
    /**
     * Set a filter for the results
     *
     * Filter can be an array containing a defined condition in an
     * array format or an StructuredQuery which will be used
     * directly as is.
     *
     * If null is sent, where will not be used.
     *
     * When array is passed, it can be one of the operations:
     * ```
     * ['not', variable] - Not operation
     * ['and', variable1, ..., variableN] - AND operation
     * ['or', variable1, ..., variableN] - OR operation
     * ['=', leftVariable, rightVariable] - Comparison operation
     * ['<', leftVariable, rightVariable] - Comparison operation
     * ['>', leftVariable, rightVariable] - Comparison operation
     * ['>=', leftVariable, rightVariable] - Comparison operation
     * ['<=', leftVariable, rightVariable] - Comparison operation
     * ['like', leftVariable, rightVariable] - Like operation
     * ['exists', variable] - Exists operation
     * ['in', leftVariable, rightVariable] - In operation
     * ['between', variable, fromVariable, toVariable] - Between operation
     * ['filter', associativeArray] - Associative array where key is the column and value is a prepared statement value.
     *                                All keys are turned into conditions joined together by AND.
     *                                All values are properly escaped and can be user inputs.
     *                                If a value is a StructuredQuery it will be pre-processed.
     * ['search', associativeArray] - Associative array where key is the column and value is a prepared statement value.
     *                                All keys are turned into conditions joined by OR.
     *                                All values are properly escaped and can be user inputs.
     *                                If a value is a StructuredQuery it will be pre-processed.
     * ['col', string] - Represents a column name or a `table.column` name.
     *                   This value is not escaped and should not contain user input.
     * ['val', value, [type]] - Represents a value which can be user input.
     *                          Type must be a value supported by the driver if not passed, type will be inferred from
     *                          the value.
     * ```
     * These operations are recursively parsed during query building.
     * A variable can be one of the following:
     * Integer, Float, Boolean, Null, String - This value is always transformed and bound into a query as a parameter.
     *                                   Depending on the driver, booleans are usually cast to
     *                                   an integer value 1 (true) or 0 (false).
     * StructuredQuery - When this is placed as a variable, driver will also process this query and join its data
     *                   and parameters into this query meaning StructuredQueries can be used as sub-queries or
     *                   for raw input (you can use Raw class for this).
     *
     * @param array|StructuredQuery|null $where
     * @return $this
     */
    public function where($where)
    {
        $this->use('where', $where);
        return $this;
    }

    /**
     * Append where to the existing one by AND-ing the previous where
     * filter to the value specified in $where.
     *
     * If the current condition in part is an `['abd', condition]`, new condition
     * will be appended to it, otherwise the previous condition and this condition will
     * be wrapped as `['abd', previous, current]`
     *
     * @param array|StructuredQuery $where
     * @return $this
     * @see ConditionTrait::appendConditionPart()
     * @see ConditionTrait::where()
     */
    public function andWhere($where)
    {
        return $this->appendConditionPart('where', 'and', $where);
    }

    /**
     * Appends a condition part to the query.
     *
     * @param string $part To which part to append the condition.
     * @param string $glue Which glue (AND/OR) to use to append the condition.
     * @param array|StructuredQuery $condition Condition to append
     * @return $this
     * @see ConditionTrait::where()
     */
    protected function appendConditionPart($part, $glue, $condition)
    {
        $current = $this->get($part);

        if (is_array($current) && ($current[0] ?? '') === $glue) {
            $current[] = $condition;
        } else if ($current === null) {
            $current = $condition;
        } else {
            $current = [$glue, $current, $condition];
        }

        $this->use($part, $current);
        return $this;
    }

    /**
     * Append where to the existing one by OR-ing the previous where
     * filter to the value specified in $where.
     *
     * If the current condition in part is an `['or', condition]`, new condition
     * will be appended to it, otherwise the previous condition and this condition will
     * be wrapped as `['or', previous, current]`
     *
     * @param array|StructuredQuery $where
     * @return $this
     * @see ConditionTrait::appendConditionPart()
     * @see ConditionTrait::where()
     */
    public function orWhere($where)
    {
        return $this->appendConditionPart('where', 'or', $where);
    }

    /**
     * Set the limit for how many results to return.
     *
     * @param int $limit
     * @return $this
     */
    public function limit($limit)
    {
        $this->use('limit', $limit);
        return $this;
    }

    /**
     * Set the offset for how many rows to skip in the result set.
     *
     * @param int $offset
     * @return $this
     */
    public function offset($offset)
    {
        $this->use('offset', $offset);
        return $this;
    }
}