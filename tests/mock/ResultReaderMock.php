<?php

namespace mock;

use ArekX\PQL\Contracts\ResultReader;

class ResultReaderMock implements ResultReader
{
    public array $rows = [];
    public int $index = 0;
    public bool $isFinalized = false;

    public static function create(array $rows): static
    {
        $instance = new static();
        $instance->rows = $rows;
        $instance->reset();
        return $instance;
    }

    public function reset(): void
    {
        $this->index = 0;
        $this->isFinalized = false;
    }

    public function getAllRows(): array
    {
        return $this->rows;
    }

    public function getColumnRows($columnIndex = 0): array
    {
        return array_map(fn($row) => $row[$columnIndex] ?? null, $this->rows);
    }

    public function getNextRow(): mixed
    {
        return $this->rows[$this->index++] ?? null;
    }

    public function getNextColumn($columnIndex = 0): mixed
    {
        $index = $this->index++;
        if (empty($this->rows[$index])) {
            return null;
        }

        return $this->rows[$index][$columnIndex];
    }

    public function finalize(): void
    {
        $this->index = count($this->rows);
        $this->isFinalized = true;
    }
}