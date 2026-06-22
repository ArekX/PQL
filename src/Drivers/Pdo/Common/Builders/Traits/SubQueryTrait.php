<?php

/**
 * Copyright 2026 Aleksandar Panic
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

namespace ArekX\PQL\Drivers\Pdo\Common\Builders\Traits;

use ArekX\PQL\Contracts\GroupedSubQuery;
use ArekX\PQL\Contracts\StructuredQuery;
use ArekX\PQL\Drivers\Pdo\Common\CommonQueryBuilderState;

trait SubQueryTrait
{
    /**
     * Build a structured query as sub-query.
     *
     * All structured queries except Raw query are wrapped in ().
     *
     * @param StructuredQuery $item Sub query to be built
     * @param CommonQueryBuilderState $state Query builder state.
     * @return string
     */
    protected function buildSubQuery(StructuredQuery $item, CommonQueryBuilderState $state): string
    {
        $result = $this->buildQuery($item, $state);
        return $item instanceof GroupedSubQuery ? "({$result})" : $result;
    }

    /**
     * Builds a structured query using parent builder from state.
     *
     * The current state is passed through so the sub query shares the outer
     * query's parameter builder. Without this the sub query would get a fresh
     * parameter builder, its placeholders would restart at :t0 and collide with
     * the outer query, and its bound values would be lost.
     *
     * @param StructuredQuery $item Query to be built
     * @param CommonQueryBuilderState $state State to be used
     * @return string
     */
    protected function buildQuery(StructuredQuery $item, CommonQueryBuilderState $state): string
    {
        return $state->getParentBuilder()->build($item, $state)->getQuery();
    }
}
