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

    protected function buildCondition($condition, MySqlQueryBuilderState $state)
    {
        static $map = null;

        if ($map === null) {
            $map = [
                'all' => fn($condition, $state) => $this->buildAssociativeCondition(' AND ', $condition, $state),
                'any' => fn($condition, $state) => $this->buildAssociativeCondition(' OR ', $condition, $state),
                'and' => fn($condition, $state) => $this->buildConjuctionCondition(' AND ', $condition, $state),
                'or' => fn($condition, $state) => $this->buildConjuctionCondition(' OR ', $condition, $state),
                'not' => fn($condition, $state) => $this->buildUnaryCondition('NOT', $condition, $state),
                'exists' => fn($condition, $state) => $this->buildUnaryCondition('EXISTS', $condition, $state),
                'in' => fn($condition, $state) => $this->buildInCondition($condition, $state),
                'between' => fn($condition, $state) => $this->buildBetweenCondition($condition, $state),
                'like' => fn($condition, $state) => $this->buildBinaryCondition(' LIKE ', $condition, $state),
                '=' => fn($condition, $state) => $this->buildBinaryCondition(' = ', $condition, $state),
                '>' => fn($condition, $state) => $this->buildBinaryCondition(' > ', $condition, $state),
                '>=' => fn($condition, $state) => $this->buildBinaryCondition(' >= ', $condition, $state),
                '<' => fn($condition, $state) => $this->buildBinaryCondition(' < ', $condition, $state),
                '<=' => fn($condition, $state) => $this->buildBinaryCondition(' <= ', $condition, $state),
                '<>' => fn($condition, $state) => $this->buildBinaryCondition(' <> ', $condition, $state),
                '!=' => fn($condition, $state) => $this->buildBinaryCondition(' <> ', $condition, $state),
                'column' => fn($condition) => $this->buildColumnCondition($condition),
                'value' => fn($condition, $state) => $this->buildValueCondition($condition, $state),
            ];
        }

        if ($condition instanceof StructuredQuery) {
            return $this->buildSubQuery($condition, $state);
        }

        if (is_array($condition)) {
            if (empty($map[$condition[0]])) {
                throw new \Exception('Unknown condition: ' . var_export($condition[0], true));
            }

            return $map[$condition[0]]($condition, $state);
        }

        throw new \Exception('Condition must be an array.');
    }

    protected function buildAssociativeCondition($glue, $condition, MySqlQueryBuilderState $state)
    {
        $result = [];
        foreach ($condition[1] as $key => $value) {
            $leftSide = $this->quoteName($key);

            if ($value instanceof StructuredQuery) {
                $result[] = $leftSide . ' IN ' . $this->buildSubQuery($value, $state);
                continue;
            }

            if (is_array($value)) {
                $result[] = $leftSide . ' IN (' . $this->buildWrapValue($value, $state) . ')';
                continue;
            }

            $result[] = $leftSide . ' = ' . $this->buildWrapValue($value, $state);
        }

        return implode($glue, $result);
    }

    protected function buildWrapValue($value, MySqlQueryBuilderState $state, $type = null)
    {
        $builder = $state->getParamsBuilder();

        if (is_array($value)) {
            $results = [];
            foreach ($value as $item) {
                $results[] = $builder->wrapValue($item, $type);
            }
            return implode(', ', $results);
        }

        return $builder->wrapValue($value, $type);
    }

    protected function buildConjuctionCondition(string $glue, $condition, MySqlQueryBuilderState $state)
    {
        $result = [];

        $max = count($condition);
        for ($i = 1; $i < $max; $i++) {
            $result[] = '(' . $this->buildCondition($condition[$i], $state) . ')';
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
        return $this->buildWrapValue(
            $condition[1] ?? null,
            $state,
            $condition[2] ?? null
        );
    }

    protected function buildUnaryCondition(string $op, $condition, MySqlQueryBuilderState $state)
    {
        if ($condition[1] instanceof StructuredQuery) {
            return $op . ' ' . $this->buildSubQuery($condition[1], $state);
        }

        return $op . ' (' . $this->buildCondition($condition[1], $state) . ')';
    }

    protected function buildBetweenCondition($condition, MySqlQueryBuilderState $state)
    {
        $of = $this->buildCondition($condition[1] ?? null, $state);
        $from = $this->buildCondition($condition[2] ?? null, $state);
        $to = $this->buildCondition($condition[3] ?? null, $state);

        return $of . ' BETWEEN ' . $from . ' AND ' . $to;
    }


    protected function buildBinaryCondition(string $operation, $condition, MySqlQueryBuilderState $state)
    {
        $left = $this->buildCondition($condition[1] ?? null, $state);
        $right = $this->buildCondition($condition[2] ?? null, $state);

        return $left . $operation . $right;
    }

    protected function buildInCondition($condition, MySqlQueryBuilderState $state)
    {
        $left = $this->buildCondition($condition[1] ?? null, $state);

        $right = $condition[2] ?? null;
        if ($right instanceof StructuredQuery) {
            return $left . ' IN ' . $this->buildSubQuery($right, $state);
        }

        return $left . ' IN (' . $this->buildCondition($right, $state) . ')';
    }
}