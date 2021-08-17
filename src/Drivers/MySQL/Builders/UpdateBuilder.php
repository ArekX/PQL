<?php

namespace ArekX\PQL\Drivers\MySQL\Builders;

use ArekX\PQL\Contracts\QueryBuilder;
use ArekX\PQL\Contracts\QueryBuilderChild;
use ArekX\PQL\Contracts\QueryBuilderState;
use ArekX\PQL\Contracts\RawQuery;
use ArekX\PQL\Contracts\StructuredQuery;
use ArekX\PQL\Data\RawSqlQuery;
use ArekX\PQL\Drivers\MySQL\Traits\BuildJoinTrait;
use ArekX\PQL\Drivers\MySQL\Traits\BuildLimitTrait;
use ArekX\PQL\Drivers\MySQL\Traits\BuildLiteralTrait;
use ArekX\PQL\Drivers\MySQL\Traits\BuildParentQuery;
use ArekX\PQL\Drivers\MySQL\Traits\BuildWhereTrait;
use ArekX\PQL\Drivers\MySQL\Traits\QuoteNameTrait;

class UpdateBuilder implements QueryBuilderChild
{
    use BuildParentQuery;
    use QuoteNameTrait;
    use BuildWhereTrait;
    use BuildLimitTrait;
    use BuildJoinTrait;
    use BuildLiteralTrait;

    public function build(StructuredQuery $query, QueryBuilderState $state): RawQuery
    {
        $structure = $query->getStructure();

        $parts = array_filter([
            $this->buildTable($structure, $state),
            $this->buildSet($structure, $state),
            $this->buildJoins($structure, $state),
            $this->buildWhere($structure, $state),
            $this->buildLimit($structure)
        ]);

        return RawSqlQuery::create(
            implode($state->get('queryGlue'), $parts),
            $state->getQueryParams()->getParams()
        );
    }

    protected function buildTable(array $structure, QueryBuilderState $state): string
    {
        $table = $structure['table'];
        if (!is_string($table)) {
            $table = $this->buildQueryByParent($table, $state)->getQuery();
        }

        return 'UPDATE ' . $table;
    }

    private function buildSet(array $structure, QueryBuilderState $state)
    {
        $set = $structure['set'];

        if ($set instanceof StructuredQuery) {
            return 'SET ' . $this->buildQueryByParent($set, $state)->getQuery();
        }

        $results = [];
        foreach ($set as $key => $value) {
            $results[] = $this->quoteName($key) . ' = ' . $this->buildLiteral($value, $state);
        }

        return 'SET ' . implode(', ', $results);
    }
}