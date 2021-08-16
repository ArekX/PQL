<?php

namespace ArekX\PQL;

use ArekX\PQL\Contracts\StructuredQuery;

class Raw implements StructuredQuery
{
    protected $query;
    protected array $params = [];

    public static function query($query, array $params = []): self
    {
        return (new Raw())->setQuery($query)->setParams($params);
    }

    public function setQuery($query): self
    {
        $this->query = $query;
        return $this;
    }

    public function setParams(array $params): self
    {
        $this->params = $params;
        return $this;
    }

    public function getStructure(): array
    {
        return [
            'query' => $this->query,
            'params' => $this->params
        ];
    }
}