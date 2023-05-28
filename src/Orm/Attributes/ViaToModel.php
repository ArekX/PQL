<?php

namespace ArekX\PQL\Orm\Attributes;

use \Attribute;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_METHOD)]
class ViaToModel
{
    public function __construct(
        public string $via,
        public string $model,
        public string $at
    )
    {
    }
}