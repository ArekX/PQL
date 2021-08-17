<?php

namespace ArekX\PQL\Drivers\MySQL\Traits;

use ArekX\PQL\Contracts\QueryBuilderState;
use ArekX\PQL\Contracts\StructuredQuery;

trait BuildAsTrait
{
    use BuildStructuredQuery;
    use QuoteNameTrait;

    protected function getAsExpression($items, QueryBuilderState $state): string
    {
        if ($items instanceof StructuredQuery) {
            return $this->buildStructuredQuery($items, $state);
        }

        if (is_string($items)) {
            $items = [$items];
        }

        $result = [];

        foreach ($items as $as => $item) {
            if ($item instanceof StructuredQuery) {
                $itemString = $this->buildStructuredQuery($item, $state);
            } else if (is_string($item)) {
                $itemString = $this->quoteName($item);
            } else {
                throw new \Exception('Invalid value passed in AS expression. Only strings or StructuredQuery is allowed.');
            }

            if (!is_integer($as)) {
                $itemString .= ' AS ' . $this->quoteName($as);
            }

            $result[] = $itemString;
        }

        return implode(', ', $result);
    }
}