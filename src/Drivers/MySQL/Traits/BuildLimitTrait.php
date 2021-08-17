<?php

namespace ArekX\PQL\Drivers\MySQL\Traits;

trait BuildLimitTrait
{
    protected function buildLimit(array $structure): ?string
    {
        $parts = [];
        if ($structure['limit'] !== null) {
            $parts[] = 'LIMIT ' . (int)$structure['limit'];
        }

        if ($structure['offset'] !== null) {
            if (empty($parts)) {
                throw new \Exception('LIMIT is required when using OFFSET.');
            }

            $parts[] = 'OFFSET ' . (int)$structure['offset'];
        }

        if (empty($parts)) {
            return null;
        }

        return implode(' ', $parts);
    }
}