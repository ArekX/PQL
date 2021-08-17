<?php

namespace ArekX\PQL\Drivers\MySQL\Traits;

use ArekX\PQL\Contracts\QueryBuilderState;
use ArekX\PQL\Contracts\StructuredQuery;

trait BuildConditionTrait
{
    use BuildLiteralTrait;
    use BuildStructuredQuery;

    protected function buildConditionExpression($expression, QueryBuilderState $state): string
    {
        if ($expression instanceof StructuredQuery) {
            return $this->buildStructuredQuery($expression, $state);
        }

        if ($this->isPrimitive($expression)) {
            return $this->buildPrimitive($expression);
        }

        if (!is_array($expression)) {
            return $state->getQueryParams()->addParam($expression);
        }

        $type = $expression[0];

        static $conditionBuilders = null;

        if ($conditionBuilders === null) {
            $conditionBuilders = [
                'multi' => fn($expression, QueryBuilderState $state) => $this->buildMultiCondition(' AND ', $expression, $state),
                'search' => fn($expression, QueryBuilderState $state) => $this->buildMultiCondition(' OR ', $expression, $state),
                'and' => fn($expression, QueryBuilderState $state) => $this->buildRelationCondition(' AND ', $expression, $state),
                'or' => fn($expression, QueryBuilderState $state) => $this->buildRelationCondition(' OR ', $expression, $state),
                'compare' => fn($expression, QueryBuilderState $state) => $this->buildCompareCondition($expression, $state),
                'column' => fn($expression, QueryBuilderState $state) => $this->quoteName($expression[1]),
                'not' => fn($expression, QueryBuilderState $state) => $this->buildNotCondition($expression, $state),
                'value' => fn($expression, QueryBuilderState $state) => $this->buildValue($expression, $state),
                'like' => fn($expression, QueryBuilderState $state) => $this->buildLikeCondition($expression, $state),
                'between' => fn($expression, QueryBuilderState $state) => $this->buildBetween($expression, $state),
                'exists' => fn($expression, QueryBuilderState $state) => $this->buildExists($expression, $state),
                'in' => fn($expression, QueryBuilderState $state) => $this->buildIn($expression, $state)
            ];
        }

        if (empty($conditionBuilders[$type])) {
            throw new \Exception("Invalid condition type: '{$type}'");
        }

        return $conditionBuilders[$type]($expression, $state);
    }

    protected function buildMultiCondition(string $operatorGlue, $expression, QueryBuilderState $state): string
    {
        $results = [];
        foreach ($expression[1] as $key => $value) {
            $leftSide = $this->quoteName($key);
            $operator = '=';

            if (is_array($value)) {
                $operator = 'IN';
                $values = [];
                foreach ($value as $item) {
                    $values[] = $this->buildLiteral($item, $state);
                }
                $rightSide = "(" . implode(', ', $values) . ")";
            } else if ($value instanceof StructuredQuery) {
                $operator = "IN";
                $rightSide = $this->buildStructuredQuery($value, $state);
            } else if ($value === null) {
                $operator = "IS";
                $rightSide = 'NULL';
            } else {
                $rightSide = $this->buildSubConditionExpression($value, $state);
            }

            $results[] = $leftSide . ' ' . $operator . ' ' . $rightSide;
        }

        return implode($operatorGlue, $results);
    }

    protected function buildRelationCondition(string $relationGlue, $expression, QueryBuilderState $state)
    {
        $max = count($expression);
        $result = [];
        for ($i = 1; $i < $max; $i++) {
            $result[] = $this->buildSubConditionExpression($expression[$i], $state);
        }

        return implode($relationGlue, $result);
    }

    protected function buildCompareCondition($expression, QueryBuilderState $state)
    {
        $leftSide = $this->buildSubConditionExpression($expression[2], $state);
        $rightSide = $this->buildSubConditionExpression($expression[3], $state);

        if ($expression[2] === '=' && is_null($expression[3])) {
            $operator = 'IS';
        } else {
            $operator = $expression[1];
        }

        return $leftSide . ' ' . $operator . ' ' . $rightSide;
    }


    protected function buildNotCondition($expression, QueryBuilderState $state)
    {
        return 'NOT ' . $this->buildSubConditionExpression($expression[1], $state);
    }

    protected function buildSubConditionExpression($expression, QueryBuilderState $state)
    {
        if ($expression instanceof StructuredQuery) {
            return $this->buildConditionExpression($expression, $state);
        }

        if ($this->isPrimitive($expression)) {
            return $this->buildPrimitive($expression);
        }

        if (is_array($expression)) {
            if ($expression[0] === 'value') {
                if (is_array($expression[1])) {
                    return '(' . $this->buildValue($expression, $state) . ')';
                }
                return $this->buildValue($expression, $state);
            } else if ($expression[0] === 'column') {
                return $this->quoteName($expression[1]);
            }
        }

        return "(" . $this->buildConditionExpression($expression, $state) . ")";
    }


    protected function buildLikeCondition($expression, QueryBuilderState $state)
    {
        $left = $this->buildSubConditionExpression($expression[1], $state);
        $right = is_string($expression[2])
            ? $state->getQueryParams()->addParam($expression[2])
            : $this->buildSubConditionExpression($expression[2], $state);

        return $left . ' LIKE ' . $right;
    }

    protected function buildBetween($expression, QueryBuilderState $state)
    {
        $of = $this->buildSubConditionExpression($expression[1], $state);

        $from = is_string($expression[2])
            ? $state->getQueryParams()->addParam($expression[2])
            : $this->buildSubConditionExpression($expression[2], $state);

        $to = is_string($expression[3])
            ? $state->getQueryParams()->addParam($expression[3])
            : $this->buildSubConditionExpression($expression[3], $state);

        return $of . ' BETWEEN ' . $from . ' AND ' . $to;
    }

    protected function buildExists($expression, QueryBuilderState $state)
    {
        return 'EXISTS ' . $this->buildSubConditionExpression($expression[1], $state);
    }

    protected function buildIn($expression, QueryBuilderState $state)
    {
        $left = $this->buildSubConditionExpression($expression[1], $state);
        $right = $this->buildSubConditionExpression($expression[2], $state);

        return $left . ' IN ' . $right;
    }

    protected function buildValue($expression, QueryBuilderState $state): string
    {
        if (!is_array($expression[1])) {
            return $this->buildLiteral($expression[1], $state);
        }

        $items = [];

        foreach ($expression[1] as $value) {
            $items[] = $this->buildLiteral($value, $state);
        }

        return implode(', ', $items);
    }
}