<?php

namespace ArekX\PQL\Orm\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class Table
{
    public function __construct(
        public ?string $name = null
    )
    {
    }
}