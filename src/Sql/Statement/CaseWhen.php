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

namespace ArekX\PQL\Sql\Statement;

use ArekX\PQL\Contracts\StructuredQuery;
use ArekX\PQL\Query;

/**
 * Represents a CASE statement.
 */
class CaseWhen extends Query
{
    /**
     * Of value to be set for the CASE
     *
     * @param array|StructuredQuery $of
     * @return $this
     */
    public function of($of)
    {
        $this->use('of', $of);
        return $this;
    }

    /**
     * When cases to be set
     *
     * @param array|StructuredQuery[] $when
     * @return $this
     */
    public function when($when)
    {
        $this->use('when', $when);
        return $this;
    }

    /**
     * Appends another WHEN case
     *
     * @param array|StructuredQuery $when Condition
     * @param array|StructuredQuery $then Result
     * @return $this
     */
    public function addWhen($when, $then)
    {
        $this->append('when', [$when, $then]);
        return $this;
    }

    /**
     * Adds else for default case
     *
     * @param array|StructuredQuery $else Else result.
     * @return $this
     */
    public function else($else)
    {
        $this->use('else', $else);
        return $this;
    }
}