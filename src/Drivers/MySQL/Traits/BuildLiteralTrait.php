<?php

namespace ArekX\PQL\Drivers\MySQL\Traits;

use ArekX\PQL\Contracts\QueryBuilderState;

trait BuildLiteralTrait
{
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
}