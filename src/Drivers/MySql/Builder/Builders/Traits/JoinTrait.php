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

use ArekX\PQL\Drivers\MySql\Builder\MySqlQueryBuilderState;

trait JoinTrait
{
    use ConditionTrait;
    use AliasTrait;

    /**
     * Build a JOIN type of the query from the passed joins.
     *
     * @param array $joins List of joins to be transformed.
     * @param MySqlQueryBuilderState $state Query builder state
     * @return string Resulting join string
     * @throws \Exception
     */
    protected function buildJoin($joins, MySqlQueryBuilderState $state)
    {
        $results = [];
        foreach ($joins as [$type, $tables, $on]) {
            $onResult = $on;

            if (!is_string($onResult) && $onResult !== null) {
                $onResult = $this->buildCondition($on, $state);
            }

            $names = $this->buildAliasedNames($tables, $state);
            $results[] = strtoupper($type) . ' JOIN ' .  $names . ($onResult ? ' ON ' . $onResult : '');
        }

        return implode($state->getQueryPartGlue(), $results);
    }
}