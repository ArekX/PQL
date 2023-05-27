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

namespace ArekX\PQL\Drivers\Pdo\MySql\Builders\Traits;

use ArekX\PQL\Contracts\GroupedSubQuery;
use ArekX\PQL\Contracts\StructuredQuery;
use ArekX\PQL\Drivers\Pdo\MySql\MySqlQueryBuilderState;

trait SubQueryTrait
{
    /**
     * Build a structured query as sub-query.
     *
     * All structured queries except Raw query are wrapped in ().
     *
     * @param StructuredQuery $item Sub query to be built
     * @param MySqlQueryBuilderState $state Query builder state.
     * @return mixed|string
     */
    protected function buildSubQuery(StructuredQuery $item, MySqlQueryBuilderState $state)
    {
        $result = $this->buildQuery($item, $state);
        return $item instanceof GroupedSubQuery ? "({$result})" : $result;
    }

    /**
     * Builds a structured query using parent builder from state.
     *
     * @param StructuredQuery $item Query to be built
     * @param MySqlQueryBuilderState $state State to be used
     * @return string
     */
    protected function buildQuery(StructuredQuery $item, MySqlQueryBuilderState $state)
    {
        return $state->getParentBuilder()->build($item)->getQuery();
    }
}
