<?php

namespace ArekX\PQL\Drivers\MySQL\Traits;

use ArekX\PQL\Contracts\QueryBuilderState;

trait BuildWhereTrait
{
    use BuildConditionTrait;

    protected function buildWhere(array $structure, QueryBuilderState $state): ?string
    {
        if (empty($structure['where'])) {
            return null;
        }

        return 'WHERE ' . $this->buildConditionExpression($structure['where'], $state);
    }
}