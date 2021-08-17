<?php

namespace ArekX\PQL;

use ArekX\PQL\Contracts\StructuredQuery;

class Insert implements StructuredQuery
{
    protected $item = null;
    protected ?array $values = null;
    protected ?array $columns = null;

    public static function into($item, $values): self
    {
        $instance = static::create()->intoItem($item);

        if (is_array($values)) {
            $instance
                ->columns(array_keys($values))
                ->values(array_values($values));
        }

        return $instance;
    }

    public static function create(): self
    {
        return new Insert();
    }

    public function intoItem($item): self
    {
        $this->item = $item;
        return $this;
    }

    public function columns($columns): self
    {
        $this->columns = $columns;
        return $this;
    }

    public function values($values): self
    {
        $this->values = $values;
        return $this;
    }

    public function getStructure(): array
    {
        return [
            'item' => $this->item,
            'columns' => $this->columns,
            'values' => $this->values
        ];
    }
}