<?php

namespace ArekX\PQL\Drivers\MySQL;

use ArekX\PQL\Contracts\Driver;
use ArekX\PQL\Contracts\RawQuery;

class MySqlDriver implements Driver
{
    public function execute(RawQuery $query): int
    {
        return 0;
    }

    public function fetchFirst(RawQuery $query)
    {
        // TODO: Implement fetchSingle() method.
    }

    public function fetchAll(RawQuery $query): array
    {
        // TODO: Implement fetchAll() method.
    }

    public function iterate(RawQuery $query): \Generator
    {
        // TODO: Implement fetchReader() method.
    }

    public function exists(RawQuery $query): bool
    {
        // TODO: Implement exists() method.
    }

    public function fetchColumn(string $resultColumn, RawQuery $query): array
    {
        // TODO: Implement fetchColumn() method.
    }

    public function fetchIndexed(string $byColumn, RawQuery $query): array
    {
        // TODO: Implement fetchIndexed() method.
    }

    public function fetchScalar(RawQuery $query)
    {
        // TODO: Implement fetchCount() method.
    }

    public function iterateRow(RawQuery $query): \Generator
    {
        // TODO: Implement iterateRow() method.
    }

    public function iterateBatch(int $batchSize, RawQuery $query): \Generator
    {
        // TODO: Implement iterateBatch() method.
    }

    public function getLastInsertId()
    {
        // TODO: Implement getLastInsertId() method.
    }
}