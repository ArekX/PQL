<?php

namespace ArekX\PQL\Contracts;

/**
 * Interface for a driver to run the raw queries.
 */
interface Driver
{
    public function run(RawQuery $query);

    public function fetchFirst(RawQuery $query);

    public function fetchAll(RawQuery $query): array;

    public function fetchExists(RawQuery $query): bool;

    public function fetchColumn(RawQuery $query, $column = null): array;

    public function fetchIndexed(string $byColumn, RawQuery $query): array;

    public function fetchList(string $keyColumn, string $valueColumn, RawQuery $query): array;

    public function fetchScalar(RawQuery $query);

    public function fetchReader(RawQuery $query, array $options = null): ResultReader;

    public function getLastInsertId();
}