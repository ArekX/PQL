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

use ArekX\PQL\Contracts\StructuredQuery;
use ArekX\PQL\Drivers\Pdo\MySql\MySqlQueryBuilderState;

trait ConditionTrait
{
    use SubQueryTrait;
    use WrapValueTrait;
    use QuoteNameTrait;

    /**
     * Build a condition into a string
     *
     * Conditions are recursively parsed.
     *
     * See where() in conditions trait for options which can be passed here.
     *
     * @see \ArekX\PQL\Sql\Query\Traits\ConditionTrait::where()
     * @param array|StructuredQuery $condition Condition to be built.
     * @param MySqlQueryBuilderState $state Query builder state.
     * @return string
     * @throws \Exception
     */
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

    /**
     * Builder for associative condition.
     *
     * Builds:
     * ```
     * ['all', ['key' => 'value']]
     * ['any', ['key' => 'value']]
     * ```
     *
     * @param string $glue Glue to be used between checks (AND/OR)
     * @param array $condition Condition to be parsed
     * @param MySqlQueryBuilderState $state Query builder state
     * @return string
     */
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

    /**
     * Builder for conjunction conditions
     *
     * Builds:
     * ```
     * ['and', expression1, expression2, ..., expressionN]
     * ['or', expression1, expression2, ..., expressionN]
     * ```
     *
     * @param string $glue Glue to be used between checks (AND/OR)
     * @param array $condition Condition to be parsed
     * @param MySqlQueryBuilderState $state Query builder state
     * @return string
     */
    protected function buildConjuctionCondition(string $glue, $condition, MySqlQueryBuilderState $state)
    {
        $result = [];

        $max = count($condition);
        for ($i = 1; $i < $max; $i++) {
            $result[] = '(' . $this->buildCondition($condition[$i], $state) . ')';
        }

        return implode($glue, $result);
    }

    /**
     * Builder for unary conditions
     *
     * Builds:
     * ```
     * ['not', expression]
     * ['exists', expression]
     * ```
     *
     * @param string $op Resulting operation (NOT/EXISTS)
     * @param array $condition Condition to be parsed
     * @param MySqlQueryBuilderState $state Query builder state
     * @return string
     */
    protected function buildUnaryCondition(string $op, $condition, MySqlQueryBuilderState $state)
    {
        if ($condition[1] instanceof StructuredQuery) {
            return $op . ' ' . $this->buildSubQuery($condition[1], $state);
        }

        return $op . ' (' . $this->buildCondition($condition[1], $state) . ')';
    }

    /**
     * Builder for IN condition
     *
     * Builds:
     * ```
     * ['in', leftExpression, rightExpression]
     * ```
     *
     * @param array $condition Condition to be parsed
     * @param MySqlQueryBuilderState $state Query builder state
     * @return string
     */
    protected function buildInCondition($condition, MySqlQueryBuilderState $state)
    {
        $left = $this->buildCondition($condition[1] ?? null, $state);

        $right = $condition[2] ?? null;
        if ($right instanceof StructuredQuery) {
            return $left . ' IN ' . $this->buildSubQuery($right, $state);
        }

        return $left . ' IN (' . $this->buildCondition($right, $state) . ')';
    }

    /**
     * Builder for BETWEEN condition
     *
     * Builds:
     * ```
     * ['between', ofExpression, fromExpression, toExpression]
     * ```
     *
     * @param array $condition Condition to be parsed
     * @param MySqlQueryBuilderState $state Query builder state
     * @return string
     */
    protected function buildBetweenCondition($condition, MySqlQueryBuilderState $state)
    {
        $of = $this->buildCondition($condition[1] ?? null, $state);
        $from = $this->buildCondition($condition[2] ?? null, $state);
        $to = $this->buildCondition($condition[3] ?? null, $state);

        return $of . ' BETWEEN ' . $from . ' AND ' . $to;
    }

    /**
     * Builder for binary conditions
     *
     * Builds:
     * ```
     * ['=', leftExpression, rightExpression]
     * ['<', leftExpression, rightExpression]
     * ['>', leftExpression, rightExpression]
     * ['<=', leftExpression, rightExpression]
     * ['>=', leftExpression, rightExpression]
     * ['!=', leftExpression, rightExpression]
     * ['<>', leftExpression, rightExpression]
     * ```
     *
     * @param string $operation Operation to be used
     * @param array $condition Condition to be parsed
     * @param MySqlQueryBuilderState $state Query builder state
     * @return string
     */
    protected function buildBinaryCondition(string $operation, $condition, MySqlQueryBuilderState $state)
    {
        $left = $this->buildCondition($condition[1] ?? null, $state);
        $right = $this->buildCondition($condition[2] ?? null, $state);

        return $left . $operation . $right;
    }

    /**
     * Builder for column
     *
     * Builds:
     * ```
     * ['column', string]
     * ```
     *
     * @param array $condition Condition to be parsed
     * @return string
     */
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


    /**
     * Builder for value (user input)
     *
     * Builds:
     * ```
     * ['value', anyValue, type]
     * ```
     *
     * @param array $condition Condition to be parsed
     * @param MySqlQueryBuilderState $state Query builder state
     * @return string
     */
    protected function buildValueCondition($condition, MySqlQueryBuilderState $state)
    {
        return $this->buildWrapValue(
            $condition[1] ?? null,
            $state,
            $condition[2] ?? null
        );
    }
}