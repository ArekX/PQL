<?php

namespace ArekX\PQL\Drivers\MySQL\Traits;

use ArekX\PQL\Contracts\QueryBuilderState;

trait BuildFromTrait
{
    use BuildAsTrait;

    protected function buildFrom(array $structure, QueryBuilderState $state): string
    {
        return 'FROM ' . $this->getAsExpression($structure['from'], $state);
    }
}