<?php

namespace ArekX\PQL\Drivers\MySQL\Builders;

use ArekX\PQL\Contracts\QueryBuilder;
use ArekX\PQL\Contracts\QueryBuilderChild;
use ArekX\PQL\Contracts\QueryBuilderState;
use ArekX\PQL\Contracts\RawQuery;
use ArekX\PQL\Contracts\StructuredQuery;
use ArekX\PQL\Data\RawSqlQuery;
use ArekX\PQL\Drivers\MySQL\Traits\BuildLiteralTrait;
use ArekX\PQL\Drivers\MySQL\Traits\BuildParentQuery;
use ArekX\PQL\Drivers\MySQL\Traits\QuoteNameTrait;
use Codeception\Step;

class InsertBuilder implements QueryBuilderChild
{
    use BuildParentQuery;
    use BuildLiteralTrait;
    use QuoteNameTrait;

    public function build(StructuredQuery $query, QueryBuilderState $state): RawQuery
    {
        $structure = $query->getStructure();

        $parts = [
            $this->buildInto($structure, $state),
            $this->buildColumns($structure, $state),
            $this->buildValues($structure, $state)
        ];

        return RawSqlQuery::create(
            implode($state->get('queryGlue'), $parts),
            $state->getQueryParams()->getParams()
        );
    }

    protected function buildInto(array $structure, QueryBuilderState $state): string
    {
        $item = $structure['item'];

        if ($item instanceof StructuredQuery) {
            return 'INSERT ' . $this->buildQueryByParent($item, $state)->getQuery();
        }

        return 'INSERT INTO ' . $this->quoteName($structure['item']);
    }

    protected function buildValues(array $structure, QueryBuilderState $state): string
    {
        $values = $structure['values'];

        if ($values instanceof StructuredQuery) {
            return 'VALUES ' . $this->buildQueryByParent($values, $state)->getQuery();
        }

        $results = [];
        foreach ($values as $value) {
            $results[] = $this->buildLiteral($value, $state);
        }

        return 'VALUES (' . implode(', ', $results) . ')';
    }

    protected function buildColumns(array $structure, QueryBuilderState $state): string
    {
        $columns = $structure['columns'];

        if ($columns instanceof StructuredQuery) {
            return $this->buildQueryByParent($columns, $state)->getQuery();
        }

        $result = [];

        foreach ($columns as $column) {
            $result[] = $this->quoteName($column);
        }

        return '(' . implode(', ', $result) . ')';
    }
}