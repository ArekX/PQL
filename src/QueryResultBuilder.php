<?php
/**
 * Copyright 2021 Aleksandar Panic
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace ArekX\PQL;

use ArekX\PQL\Contracts\ResultBuilder;
use ArekX\PQL\Contracts\ResultReader;

class QueryResultBuilder implements ResultBuilder
{
    protected ResultReader $reader;

    protected $pipeline = [];

    public static function create()
    {
        return new static();
    }

    public function useReader(ResultReader $reader): ResultBuilder
    {
        $this->reader = $reader;
        return $this;
    }

    public function all()
    {
        $this->reader->reset();
        $results = $this->applyPipeline($this->reader->getAllRows());
        $this->reader->finalize();
        return $results;
    }

    public function first()
    {
        $this->reader->reset();
        $result = $this->applyPipeline($this->reader->getNextRow());
        $this->reader->finalize();
        return $result;
    }

    public function scalar($index = 0)
    {
        $this->reader->reset();
        $result = $this->applyPipeline($this->reader->getNextColumn($index));
        $this->reader->finalize();
        return $result;
    }

    public function column()
    {
        $this->reader->reset();
        $result = $this->applyPipeline($this->reader->getAllColumns());
        $this->reader->finalize();
        return $result;
    }

    public function exists(): bool
    {
        $this->reader->reset();
        $result = $this->reader->getNextColumn();
        $this->reader->finalize();
        return !empty($result);
    }

    public function list($keyColumn, $valueColumn)
    {
        $results = $this->all();

        $list = [];
        foreach ($results as $row) {
            $list[$row[$keyColumn]] = $row[$valueColumn];
        }

        return $list;
    }

    public function clearPipeline(): ResultBuilder
    {
        $this->pipeline = [];
    }

    protected function applyPipeline($result)
    {
        foreach ($this->pipeline as $runMethod) {
            $result = $runMethod($result);
        }

        return $result;
    }

    public function pipe(callable $method): ResultBuilder
    {
        $this->pipeline[] = $method;
        return $this;
    }

    public function pipeIndexBy(string $column): ResultBuilder
    {
        return $this->pipe(function ($input) use ($column) {
            $results = [];

            foreach ($input as $row) {
                if (!is_array($row) || !array_key_exists($column, $row)) {
                    throw new \Exception("Column '{$column}' is not defined in result.");
                }
                $results[$row[$column]] = $row;
            }

            return $results;
        });
    }

    public function pipeSort(callable $sorter): ResultBuilder
    {
        return $this->pipe(function ($input) use ($sorter) {
            usort($input, $sorter);
            return $input;
        });
    }

    public function pipeMap(callable $mapper): ResultBuilder
    {
        return $this->pipe(function ($input) use ($mapper) {
            return array_map($mapper, $input);
        });
    }

    public function pipeReduce(callable $reducer): ResultBuilder
    {
        return $this->pipe(function ($input) use ($reducer) {
            return array_reduce($input, $reducer);
        });
    }

    public function pipeFilter(callable $filter): ResultBuilder
    {
        return $this->pipe(function ($input) use ($filter) {
            return array_filter($input, $filter);
        });
    }
}