<?php

namespace ArekX\PQL;

use ArekX\PQL\Contracts\StructuredQuery;

class Insert implements StructuredQuery
{
    protected ?string $table = null;
    protected ?array $values = null;

    public static function create(): self
    {
        return new Insert();
    }

    public function into(string $table): self
    {
        $this->table = $table;
        return $this;
    }

    public function values(array $values): self
    {
        $this->values = $values;
        return $this;
    }

    public function getStructure(): array
    {
        return [
            'table' => $this->table,
            'values' => $this->values
        ];
    }
}