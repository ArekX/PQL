<?php

namespace mock;

use ArekX\PQL\Contracts\ResultReader;

class ResultReaderMock implements ResultReader
{
    public $rows = [];
    public $index = 0;
    public $isFinalized = false;

    public static function create(array $rows)
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

    public function getAllColumns($columnIndex = 0): array
    {
        return array_map(fn($row) => $row[$columnIndex] ?? null, $this->rows);
    }

    public function getNextRow()
    {
        return $this->rows[$this->index++] ?? null;
    }

    public function getNextColumn($columnIndex = 0)
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