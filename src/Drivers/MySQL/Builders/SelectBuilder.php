<?php

namespace ArekX\PQL\Drivers\MySQL\Builders;

use ArekX\PQL\Contracts\QueryBuilder;
use ArekX\PQL\Contracts\RawQuery;
use ArekX\PQL\Contracts\StructuredQuery;
use ArekX\PQL\Data\RawSqlQuery;

class SelectBuilder implements QueryBuilder
{
    public function build(StructuredQuery $query): RawQuery
    {
        $structure = $query->getStructure();

        $parts = [
            $this->buildSelectPart($structure),
            $this->buildFrom($structure),
            $this->buildJoins($structure)
        ];

        $rawQuery = [];
        $params = [];

        foreach ($parts as [$queryPart, $paramsPart]) {
            if (empty($queryPart)) {
                continue;
            }

            $rawQuery[] = $queryPart;
            $params += $paramsPart;
        }

        return new RawSqlQuery(implode(PHP_EOL, $rawQuery), $params);
    }

    protected function buildSelectPart(array $structure): array
    {
        $columns = is_string($structure['select']) ? [$structure['select']] : $structure['select'];
        return ['SELECT ' . $this->getAsExpression($columns), []];
    }

    protected function quoteName($name): string
    {
        if (strpos($name, "'") !== false) {
            return $name;
        }

        return preg_replace("/([a-z_][a-zA-Z0-9_]*)/", "`$1`", $name);
    }

    protected function getAsExpression(array $items): string
    {
        $result = [];

        foreach ($items as $as => $item) {
            if (!is_string($item)) {
                $itemString = $this->buildExpression($item);
            } else {
                $itemString = $this->quoteName($item);
            }

            if (!is_integer($as)) {
                $itemString .= ' AS ' . $this->quoteName($as);
            }

            $result[] = $itemString;
        }

        return implode(',' . PHP_EOL, $result);
    }

    protected function buildExpression($expression): string
    {
        return 'not implemented';
    }

    protected function buildFrom(array $structure): array
    {
        $from = is_string($structure['from']) ? [$structure['from']] : $structure['from'];
        return ['FROM ' . $this->getAsExpression($from), []];
    }

    protected function buildJoins(array $structure): array
    {
        return ['JOIN idemo niis', []];
    }
}