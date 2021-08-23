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

namespace ArekX\PQL\Drivers\MySql\Builder\Builders\Traits;

use ArekX\PQL\Contracts\StructuredQuery;
use ArekX\PQL\Drivers\MySql\Builder\MySqlQueryBuilderState;

trait AliasTrait
{
    use QuoteNameTrait;
    use SubQueryTrait;

    /**
     * Build AS part containing names in SQL.
     *
     * If string is passed to this method, the name is quoted as is using quoteName()
     *
     * If array is passed then all values are quoted using quote names and if key of the specific
     * array item is string then it is added to the end AS. So following:
     * ```
     * ['alias' => 'table']
     * ```
     * Results in `table` AS `alias`.
     *
     * If a structured query is passed as a value, it is also parsed.
     *
     * If structured query is passed as a name part, it will be processed too.
     *
     * @see QuoteNameTrait::quoteName()
     * @see SubQueryTrait::buildSubQuery()
     * @param string|array|StructuredQuery $namePart Part to be processed.
     * @param MySqlQueryBuilderState $state State of the builder
     * @return string
     */
    protected function buildAliasedNames($namePart, MySqlQueryBuilderState $state)
    {
        if ($namePart instanceof StructuredQuery) {
            return $this->buildSubQuery($namePart, $state);
        }

        if (is_string($namePart)) {
            return $this->quoteName($namePart);
        }

        $items = [];

        foreach ($namePart as $as => $item) {
            if ($item instanceof StructuredQuery) {
                $item = $this->buildSubQuery($item, $state);
            } else {
                $item = $this->quoteName($item);
            }


            if (is_string($as)) {
                $item .= " AS " . $this->quoteName($as);
            }

            $items[] = $item;
        }

        return implode(', ', $items);
    }
}