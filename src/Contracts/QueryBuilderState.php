<?php

namespace ArekX\PQL\Contracts;

interface QueryBuilderState
{
    public function get($name);
    public function set($name, $value);

    public function getQueryParams(): QueryParams;

    public function setQueryParams(QueryParams $params);
}