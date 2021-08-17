<?php

namespace ArekX\PQL\Contracts;

interface Driver
{
    public function execute(RawQuery $query): int;

    public function fetchFirst(RawQuery $query);

    public function fetchAll(RawQuery $query): array;

    public function exists(RawQuery $query): bool;

    public function fetchColumn(RawQuery $query, $column = null): array;

    public function fetchIndexed(string $byColumn, RawQuery $query): array;

    public function fetchScalar(RawQuery $query);

    public function getLastInsertId();

    public function iterateRow(RawQuery $query): \Generator;

    public function iterateBatch(int $batchSize, RawQuery $query): \Generator;
}