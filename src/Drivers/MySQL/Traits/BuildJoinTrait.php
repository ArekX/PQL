<?php

namespace ArekX\PQL\Drivers\MySQL\Traits;

use ArekX\PQL\Contracts\QueryBuilderState;

trait BuildJoinTrait
{
    use BuildConditionTrait;
    use BuildAsTrait;

    protected function buildJoins(array $structure, QueryBuilderState $state): ?string
    {
        if (empty($structure['join'])) {
            return null;
        }

        $joins = [];

        foreach ($structure['join'] as [$type, $table, $on]) {
            $conditionSql = is_string($on)
                ? $on
                : $this->buildConditionExpression($on, $state);
            $asSql = $this->getAsExpression($table, $state);
            $joins[] = strtoupper($type) . ' JOIN ' . $asSql . ' ON ' . $conditionSql;
        }

        return implode($state->get('queryGlue'), $joins);
    }
}