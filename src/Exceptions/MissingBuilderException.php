<?php

namespace ArekX\PQL\Exceptions;

use ArekX\PQL\Contracts\StructuredQuery;
use Throwable;

class MissingBuilderException extends \Exception
{
    public StructuredQuery $query;

    public function __construct(StructuredQuery $query)
    {
        $this->query = $query;

        parent::__construct('Missing builder for this query.', 1);
    }
}