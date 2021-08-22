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
use ArekX\PQL\Sql\Query\Traits\ConditionTrait;
use ArekX\PQL\Sql\Query\Traits\ConfigureTrait;
use ArekX\PQL\Sql\Query\Traits\JoinTrait;

/**
 * Represents a delete query to delete items from the
 * driver.
 */
class Delete extends Query
{
    use ConditionTrait;
    use JoinTrait;
    use ConfigureTrait;

    /**
     * Set the place from which to delete data.
     *
     * SQL Injection Warning: Value in this function is not usually escaped in the driver
     * and should not be used to pass values from the user input to it. If you need to pass
     * and escape query use Raw query.
     *
     * If a StructuredQuery is passed, it is parsed as is.
     *
     * @param string|StructuredQuery $from Table or other destination to delete data from.
     * @return $this
     */
    public function from($from)
    {
        $this->use('from', $from);
        return $this;
    }
}