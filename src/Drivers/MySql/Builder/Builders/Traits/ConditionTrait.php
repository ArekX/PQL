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

trait ConditionTrait
{
    use SubQueryTrait;
    use QuoteNameTrait;

    protected function resolveCondition($condition, MySqlQueryBuilderState $state)
    {
        if ($condition instanceof StructuredQuery) {
            return $this->buildSubQuery($condition, $state);
        }

        if (is_array($condition)) {
            switch($condition[0]) {
                case 'all':
                    return $this->buildAssociativeCondition(' AND ',$condition, $state);
                case 'any':
                    return $this->buildAssociativeCondition(' OR ',$condition, $state);
                case 'and':
                    return $this->buildConjuctionCondition(' AND ', $condition, $state);
                case 'or':
                    return $this->buildConjuctionCondition(' OR ', $condition, $state);
                case 'column':
                    return $this->buildColumnCondition($condition);
                case 'value':
                    return $this->buildValueCondition($condition, $state);
            }

            throw new \Exception('Unknown condition: ' . var_export($condition[0], true));
        }

        throw new \Exception('Condition must be an array.');
    }

    protected function buildConjuctionCondition(string $glue, $condition, MySqlQueryBuilderState $state)
    {
        $result = [];

        $max = count($condition);
        for($i = 1; $i < $max; $i++) {
            $result[] = '(' . $this->resolveCondition($condition[$i], $state) . ')';
        }

        return implode($glue, $result);
    }

    protected function buildAssociativeCondition($glue, $condition, MySqlQueryBuilderState $state)
    {
        $result = [];
        foreach ($condition[1] as $key => $value) {
            $result[] = $this->quoteName($key) . ' = ' . $state->getParamsBuilder()->wrapValue($value);
        }

        return implode($glue, $result);
    }

    protected function buildColumnCondition($condition)
    {
        $column = $condition[1] ?? null;

        if (empty($column)) {
            throw new \Exception('Column name must be set.');
        } else if (!is_string($column)) {
            throw new \Exception('Column name must be a string.');
        }

        return $this->quoteName($column);
    }

    protected function buildValueCondition($condition, MySqlQueryBuilderState $state)
    {
        return $state->getParamsBuilder()->wrapValue($condition[1] ?? null, $condition[2] ?? null);
    }
}