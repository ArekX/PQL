<?php

namespace ArekX\PQL\Contracts;

interface RawQuery
{
    public function getQuery();

    public function getParams(): array;
}