<?php

namespace ArekX\PQL\Drivers\MySQL\Builders;

use ArekX\PQL\Contracts\QueryBuilder;
use ArekX\PQL\Contracts\QueryBuilderChild;
use ArekX\PQL\Contracts\QueryBuilderState;
use ArekX\PQL\Contracts\RawQuery;
use ArekX\PQL\Contracts\StructuredQuery;
use ArekX\PQL\Data\RawSqlQuery;
use ArekX\PQL\Raw;

class SelectBuilder implements QueryBuilderChild
{
    protected QueryBuilder $parent;

    public function build(StructuredQuery $query, QueryBuilderState $state): RawQuery
    {
        $structure = $query->getStructure();

        $parts = array_filter([
            $this->buildSelectPart($structure, $state),
            $this->buildFrom($structure, $state),
            $this->buildJoins($structure, $state),
            $this->buildWhere($structure, $state),
            $this->buildLimit($structure)
        ]);

        return RawSqlQuery::create(implode(PHP_EOL, $parts), $state->getQueryParams()->getParams());
    }

    protected function buildSelectPart(array $structure, QueryBuilderState $state): string
    {
        return 'SELECT ' . $this->getAsExpression($structure['select'], $state);
    }

    protected function quoteName($name): string
    {
        if (strpos($name, "'") !== false) {
            return $name;
        }

        return preg_replace("/([a-z_][a-zA-Z0-9_]*)/", "`$1`", $name);
    }

    protected function getAsExpression($items, QueryBuilderState $state): string
    {
        if ($items instanceof StructuredQuery) {
            return $this->buildStructuredQuery($items, $state);
        }

        if (is_string($items)) {
            $items = [$items];
        }

        $result = [];

        foreach ($items as $as => $item) {
            if ($item instanceof StructuredQuery) {
                $itemString = $this->buildStructuredQuery($item, $state);
            } else if (is_string($item)) {
                $itemString = $this->quoteName($item);
            } else {
                throw new \Exception('Invalid value passed in AS expression. Only strings or StructuredQuery is allowed.');
            }

            if (!is_integer($as)) {
                $itemString .= ' AS ' . $this->quoteName($as);
            }

            $result[] = $itemString;
        }

        return implode(',' . PHP_EOL, $result);
    }

    protected function buildStructuredQuery(StructuredQuery $query, QueryBuilderState $state): string
    {
        $result = $this->parent->build($query, $state);

        if ($query instanceof Raw) {
            return $result->getQuery();
        }

        return "(" . $result->getQuery() . ")";
    }

    protected function isPrimitive($expression)
    {
       return is_int($expression) || is_float($expression) || is_bool($expression) || is_null($expression);
    }

    protected function buildPrimitive($expression)
    {
        if (is_bool($expression) || is_int($expression)) {
            return (int)$expression;
        }

        if (is_float($expression)) {
            return (float)$expression;
        }

        if (is_null($expression)) {
            return 'NULL';
        }
    }

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
                    $values[] = $state->getQueryParams()->addParam($item);
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

    protected function buildFrom(array $structure, QueryBuilderState $state): string
    {
        return 'FROM ' . $this->getAsExpression($structure['from'], $state);
    }

    protected function buildJoins(array $structure, QueryBuilderState $state): ?string
    {
        if (empty($structure['join'])) {
            return null;
        }

        $joins = [];

        foreach ($structure['join'] as [$type, $table, $on]) {
            $conditionSql = $this->buildConditionExpression($on, $state);
            $asSql = $this->getAsExpression($table, $state);
            $joins[] = strtoupper($type) . ' JOIN ' . $asSql . ' ON ' . $conditionSql;
        }

        return implode(PHP_EOL, $joins);
    }

    protected function buildLimit(array $structure): ?string
    {
        $parts = [];
        if ($structure['limit'] !== null) {
            $parts[] = 'LIMIT ' . (int)$structure['limit'];
        }

        if ($structure['offset'] !== null) {
            if (empty($parts)) {
                throw new \Exception('LIMIT is required when using OFFSET.');
            }

            $parts[] = 'OFFSET ' . (int)$structure['offset'];
        }

        if (empty($parts)) {
            return null;
        }

        return implode(' ', $parts);
    }

    protected function buildRelationCondition(string $relationGlue, $expression, QueryBuilderState $state)
    {
        $max = count($expression);
        $result = [];
        for($i = 1; $i < $max; $i++) {
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

    protected function buildWhere(array $structure, QueryBuilderState $state): ?string
    {
        if (empty($structure['where'])) {
            return null;
        }

        return 'WHERE ' . $this->buildConditionExpression($structure['where'], $state);
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
            return $state->getQueryParams()->addParam($expression[1]);
        }

        $items = [];

        foreach ($expression[1] as $value) {
            $items[] = $state->getQueryParams()->addParam($value);
        }

        return implode(', ', $items);
    }

    public function setParent(QueryBuilder $parent)
    {
        $this->parent = $parent;
    }
}