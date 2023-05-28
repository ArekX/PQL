<?php

namespace ArekX\PQL\Orm\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_METHOD)]
class Column
{
    public function __construct(
        public ?string $columnName = null
    )
    {
    }
}