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

/**
 * Result builder for query.
 */
class QueryResultBuilder implements ResultBuilder
{
    /**
     * Result reader which is used to read all results from.
     *
     * @var ResultReader
     */
    protected ResultReader $reader;

    /**
     * List of pipeline methods to apply when calling result()
     *
     * @see QueryResultBuilder::result()
     * @var array
     */
    protected array $pipelines = [];

    /**
     * Create new instance query result builder.
     * @param ResultReader|null $reader Reader to be used
     * @return static
     */
    public static function create(ResultReader $reader = null): static
    {
        return new static($reader);
    }

    /**
     * Create new instance query result builder.
     * @param ResultReader|null $reader Reader to be used
     */
    public function __construct(ResultReader $reader = null)
    {
        if ($reader) {
            $this->useReader($reader);
        }
    }

    /**
     * @inheritDoc
     */
    public function useReader(ResultReader $reader): static
    {
        $this->reader = $reader;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function first(): mixed
    {
        $result = $this->reader->getNextRow();
        $this->reader->finalize();
        return $result;
    }

    /**
     * @inheritDoc
     */
    public function scalar($index = 0): mixed
    {
        $result = $this->reader->getNextColumn($index);
        $this->reader->finalize();
        return $result;
    }

    /**
     * @inheritDoc
     */
    public function column($index = 0): array
    {
        $result = $this->reader->getColumnRows($index);
        $this->reader->finalize();
        return $result;
    }

    /**
     * @inheritDoc
     */
    public function exists(): bool
    {
        $result = $this->reader->getNextColumn();
        $this->reader->finalize();
        return $result !== null;
    }

    /**
     * @inheritDoc
     */
    public function clearPipeline(): static
    {
        $this->pipelines = [];
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function pipeIndexBy(string $column): static
    {
        return $this->pipeReduce(function ($result, $row) use ($column) {
            $result[$row[$column]] = $row;
            return $result;
        });
    }

    /**
     * @inheritDoc
     */
    public function pipeReduce(callable $reducer, $initialValue = null): static
    {
        $this->pipelines[] = fn($results) => array_reduce($results, $reducer, $initialValue);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function pipeSort(callable $sorter): static
    {
        $this->pipelines[] = function ($results) use ($sorter) {
            usort($results, $sorter);
            return $results;
        };

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function pipeMap(callable $mapper): static
    {
        $this->pipelines[] = fn($results) => array_map($mapper, $results);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function pipeFilter(callable $filter): static
    {
        $this->pipelines[] = fn($results) => array_values(array_filter($results, $filter));
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function result(): mixed
    {
        $result = $this->all();

        foreach ($this->pipelines as $pipelineMethod) {
            $result = $pipelineMethod($result, $this);
        }

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function all(): array
    {
        $result = $this->reader->getAllRows();
        $this->reader->finalize();
        return $result;
    }

    /**
     * @inheritDoc
     */
    public function pipeListBy(string $keyColumn, string $valueColumn): static
    {
        return $this->pipeReduce(function ($previous, $row) use ($keyColumn, $valueColumn) {

            if (!array_key_exists($keyColumn, $row) || !array_key_exists($valueColumn, $row)) {
                throw new \InvalidArgumentException('$keyColumn and $valueColumn must exist in the result.');
            }

            $previous[$row[$keyColumn]] = $row[$valueColumn];

            return $previous;
        }, []);
    }
}
