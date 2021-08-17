<?php

namespace ArekX\PQL\Drivers\MySQL\Builders;

use ArekX\PQL\Contracts\QueryBuilder;
use ArekX\PQL\Contracts\QueryBuilderChild;
use ArekX\PQL\Contracts\QueryBuilderState;
use ArekX\PQL\Contracts\RawQuery;
use ArekX\PQL\Contracts\StructuredQuery;
use ArekX\PQL\Data\RawSqlQuery;
use Codeception\Step;

class InsertBuilder implements QueryBuilderChild
{
    protected QueryBuilder $parent;

    public function build(StructuredQuery $query, QueryBuilderState $state): RawQuery
    {
        $structure = $query->getStructure();

        $parts = [
            $this->buildInto($structure, $state),
            $this->buildColumns($structure, $state),
            $this->buildValues($structure, $state)
        ];

        return RawSqlQuery::create(implode(PHP_EOL, $parts), $state->getQueryParams()->getParams());
    }


    public function setParent(QueryBuilder $parent)
    {
        $this->parent = $parent;
    }

    protected function quoteName($name): string
    {
        if (strpos($name, "'") !== false) {
            return $name;
        }

        return preg_replace("/([a-z_][a-zA-Z0-9_]*)/", "`$1`", $name);
    }

    protected function buildInto(array $structure, QueryBuilderState $state): string
    {
        $item = $structure['item'];

        if ($item instanceof StructuredQuery) {
            return 'INSERT ' . $this->parent->build($item, $state)->getQuery();
        }

        return 'INSERT INTO ' . $this->quoteName($structure['item']);
    }

    protected function buildValues(array $structure, QueryBuilderState $state): string
    {
        $values = $structure['values'];

        if ($values instanceof StructuredQuery) {
            return 'VALUES ' . $this->parent->build($values, $state)->getQuery();
        }

        $results = [];
        foreach ($values as $value) {
            $results[] = $this->buildLiteral($value, $state);
        }

        return 'VALUES (' . implode(', ', $results) . ')';
    }

    protected function buildLiteral($value, QueryBuilderState $state)
    {
        if ($this->isPrimitive($value)) {
            return $this->buildPrimitive($value);
        }

        return $state->getQueryParams()->addParam($value);
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

    protected function buildColumns(array $structure, QueryBuilderState $state)
    {
        $columns = $structure['columns'];

        if ($columns instanceof StructuredQuery) {
            return $this->parent->build($columns, $state);
        }

        $result = [];

        foreach ($columns as $column) {
            $result[] = $this->quoteName($column);
        }

        return '(' . implode(', ', $result) . ')';
    }
}