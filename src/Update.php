<?php

namespace ArekX\PQL;

use ArekX\PQL\Contracts\ConditionQuery;
use ArekX\PQL\Contracts\JoinQuery;
use ArekX\PQL\Contracts\StructuredQuery;
use ArekX\PQL\Traits\JoinConditionTrait;
use ArekX\PQL\Traits\WhereConditionTrait;

class Update implements StructuredQuery, ConditionQuery, JoinQuery
{
    use WhereConditionTrait;
    use JoinConditionTrait;

    protected ?string $table = null;
    protected ?array $values = null;

    public static function create(): self
    {
        return new Update();
    }

    public function table(string $table): self
    {
        $this->table = $table;
        return $this;
    }

    public function set(array $valueMap): self
    {
        $this->values = $valueMap;
        return $this;
    }

    public function getStructure(): array
    {
        return [
            'table' => $this->table,
            'set' => $this->values,
            'where' => $this->where,
            'join' => $this->join
        ];
    }
}