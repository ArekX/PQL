<?php

namespace ArekX\PQL\Drivers\MySQL\Builders;

use ArekX\PQL\Contracts\QueryBuilderChild;
use ArekX\PQL\Contracts\QueryBuilderState;
use ArekX\PQL\Contracts\RawQuery;
use ArekX\PQL\Contracts\StructuredQuery;
use ArekX\PQL\Data\RawSqlQuery;
use ArekX\PQL\Drivers\MySQL\Traits\BuildAsTrait;
use ArekX\PQL\Drivers\MySQL\Traits\BuildFromTrait;
use ArekX\PQL\Drivers\MySQL\Traits\BuildJoinTrait;
use ArekX\PQL\Drivers\MySQL\Traits\BuildLimitTrait;
use ArekX\PQL\Drivers\MySQL\Traits\BuildLiteralTrait;
use ArekX\PQL\Drivers\MySQL\Traits\BuildParentQuery;
use ArekX\PQL\Drivers\MySQL\Traits\BuildStructuredQuery;
use ArekX\PQL\Drivers\MySQL\Traits\BuildWhereTrait;
use ArekX\PQL\Drivers\MySQL\Traits\QuoteNameTrait;

class SelectBuilder implements QueryBuilderChild
{
    use BuildParentQuery;
    use BuildAsTrait;
    use BuildJoinTrait;
    use BuildFromTrait;
    use QuoteNameTrait;
    use BuildLiteralTrait;
    use BuildWhereTrait;
    use BuildLimitTrait;
    use BuildStructuredQuery;

    public function build(StructuredQuery $query, QueryBuilderState $state): RawQuery
    {
        $structure = $query->getStructure();

        $parts = array_filter([
            $this->buildSelectPart($structure, $state),
            $this->buildFrom($structure, $state),
            $this->buildJoins($structure, $state),
            $this->buildWhere($structure, $state),
            $this->buildGroupBy($structure, $state),
            $this->buildOrderBy($structure, $state),
            $this->buildHaving($structure, $state),
            $this->buildLimit($structure)
        ]);

        return RawSqlQuery::create(
            implode($state->get('queryGlue'), $parts),
            $state->getQueryParams()->getParams()
        );
    }

    protected function buildSelectPart(array $structure, QueryBuilderState $state): string
    {
        return 'SELECT ' . $this->getAsExpression($structure['select'], $state);
    }

    protected function buildGroupBy(array $structure, QueryBuilderState $state): ?string
    {
        if (empty($structure['group'])) {
            return null;
        }

        $group = $structure['group'];

        if (is_string($group)) {
            $group = $this->quoteName($group);
        } else if ($group instanceof StructuredQuery) {
            $group = $this->buildStructuredQuery($group, $state);
        } else {
            $result = [];
            foreach ($group as $name) {
                $result[] = $this->quoteName($name);
            }
            $group = implode(', ', $result);
        }

        return 'GROUP BY ' . $group;
    }

    protected function buildOrderBy(array $structure, QueryBuilderState $state): ?string
    {
        if (empty($structure['order'])) {
            return null;
        }

        $order = $structure['order'];

        if ($order instanceof StructuredQuery) {
            $order = $this->buildStructuredQuery($order, $state);
        } else {
            $result = [];
            foreach ($order as $column => $value) {
                $result[] = $this->quoteName($column) . ' ' . strtoupper($value);
            }
            $order = implode(', ', $result);
        }

        return 'ORDER BY ' . $order;
    }

    protected function buildHaving(array $structure, QueryBuilderState $state)
    {
        if (empty($structure['having'])) {
            return null;
        }

        return 'HAVING ' . $this->buildConditionExpression($structure['having'], $state);
    }
}