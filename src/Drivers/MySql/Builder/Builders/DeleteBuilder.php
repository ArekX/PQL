<?php

namespace ArekX\PQL\Drivers\MySql\Builder\Builders;

class DeleteBuilder extends QueryPartBuilder
{
    protected function getInitialParts(): array
    {
        return ['DELETE'];
    }

    protected function getPartBuilders(): array
    {
        return [
            'from' => fn($part) => $this->buildFrom($part)
        ];
    }

    protected function buildFrom($part)
    {
        return 'FROM ' . $part;
    }

    protected function getRequiredParts(): array
    {
        return ['from'];
    }
}