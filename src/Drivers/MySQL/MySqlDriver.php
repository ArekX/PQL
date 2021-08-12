<?php

namespace ArekX\PQL\Drivers\MySQL;

use ArekX\PQL\Contracts\RawQuery;

class MySqlDriver implements \ArekX\PQL\Contracts\Driver
{
    public function execute(RawQuery $query): int
    {
        // TODO: Implement execute() method.
    }

    public function fetchSingle(RawQuery $query)
    {
        // TODO: Implement fetchSingle() method.
    }

    public function fetchAll(RawQuery $query): array
    {
        // TODO: Implement fetchAll() method.
    }

    public function fetchReader(RawQuery $query): \Generator
    {
        // TODO: Implement fetchReader() method.
    }
}