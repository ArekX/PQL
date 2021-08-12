<?php

namespace ArekX\PQL\Contracts;

interface Driver
{
    public function execute(RawQuery $query): int;

    public function fetchSingle(RawQuery $query);

    public function fetchAll(RawQuery $query): array;

    public function fetchReader(RawQuery $query): \Generator;
}