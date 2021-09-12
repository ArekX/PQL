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

    public function useReader(ResultReader $reader): ResultBuilder
    {
        $this->reader = $reader;
        return $this;
    }

    public function all()
    {
        $result = $this->reader->getAllRows();
        $this->reader->finalize();
        return $result;
    }

    public function first()
    {
        $result = $this->reader->getNextRow();
        $this->reader->finalize();
        return $result;
    }

    public function scalar($index = 0)
    {
        $result = $this->reader->getNextColumn($index);
        $this->reader->finalize();
        return $result;
    }

    public function column($index = 0)
    {
        $result = $this->reader->getAllColumns($index);
        $this->reader->finalize();
        return $result;
    }

    public function exists(): bool
    {
        $result = $this->reader->getNextColumn();
        $this->reader->finalize();
        return $result !== null;
    }

    public function list($keyColumn, $valueColumn)
    {
        $map = [];

        $rows = $this->all();

        foreach ($rows as $row) {
            if (!array_key_exists($keyColumn, $row) || !array_key_exists($valueColumn, $row)) {
                throw new \Exception('$keyColumn and $valueColumn must exist in the result.');
            }

            $map[$row[$keyColumn]] = $row[$valueColumn];
        }

        return $map;
    }

    public function clearPipeline(): ResultBuilder
    {
        // TODO: Implement clearPipeline() method.
    }

    public function pipeIndexBy(string $column): ResultBuilder
    {
        // TODO: Implement pipeIndexBy() method.
    }

    public function pipeSort(callable $sorter): ResultBuilder
    {
        // TODO: Implement pipeSort() method.
    }

    public function pipeMap(callable $mapper): ResultBuilder
    {
        // TODO: Implement pipeMap() method.
    }

    public function pipeReduce(callable $reducer): ResultBuilder
    {
        // TODO: Implement pipeReduce() method.
    }

    public function pipeFilter(callable $filter): ResultBuilder
    {
        // TODO: Implement pipeFilter() method.
    }
}