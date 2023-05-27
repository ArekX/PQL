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

trait JoinTrait
{
    /**
     * Add an INNER type join to the query.
     *
     * This function is a helper function for join with type set to `inner`.
     *
     * @param array|StructuredQuery $withSource Source to join with
     * @param array|StructuredQuery|null $condition Condition to join on
     * @return $this
     * @see JoinTrait::join()
     */
    public function innerJoin($withSource, $condition = null)
    {
        return $this->join('inner', $withSource, $condition);
    }

    /**
     * Add a join part to the query for a specific type of join
     * source to join with and a condition of the join.
     *
     * If $withSource is passed as an associative array it will be parsed as
     * an array where key is the AS alias and value is the name of the table
     * or a sub-query if StructuredQuery is passed.
     *
     * @param string $type Type of the join, types depend on the types that driver supports.
     * @param array|StructuredQuery $withSource Source to join with.
     * @param array|StructuredQuery|null $condition Condition to be set for the join part.
     * @return $this
     * @see ConditionTrait::where() for $condition format
     */
    public function join($type, $withSource, $condition = null)
    {
        $this->append('join', [$type, $withSource, $condition]);
        return $this;
    }

    /**
     * Add an LEFT type join to the query.
     *
     * This function is a helper function for join with type set to `left`.
     *
     * @param array|StructuredQuery $withSource Source to join with
     * @param array|StructuredQuery|null $condition Condition to join on
     * @return $this
     * @see JoinTrait::join()
     */
    public function leftJoin($withSource, $condition)
    {
        return $this->join('left', $withSource, $condition);
    }

    /**
     * Add an RIGHT type join to the query.
     *
     * This function is a helper function for join with type set to `right`.
     *
     * @param array|StructuredQuery $withSource Source to join with
     * @param array|StructuredQuery|null $condition Condition to join on
     * @return $this
     * @see JoinTrait::join()
     */
    public function rightJoin($withSource, $condition)
    {
        return $this->join('right', $withSource, $condition);
    }

    /**
     * Add an FULL OUTER type join to the query.
     *
     * This function is a helper function for join with type set to `full outer`.
     *
     * @param array|StructuredQuery $withSource Source to join with
     * @param array|StructuredQuery|null $condition Condition to join on
     * @return $this
     * @see JoinTrait::join()
     */
    public function fullOuterJoin($withSource, $condition)
    {
        return $this->join('full outer', $withSource, $condition);
    }
}

