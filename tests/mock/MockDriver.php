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

namespace mock;

use ArekX\PQL\Contracts\Driver;
use ArekX\PQL\Contracts\RawQuery;
use ArekX\PQL\Contracts\ResultBuilder;
use ArekX\PQL\Contracts\ResultReader;
use ArekX\PQL\QueryResultBuilder;

class MockDriver implements Driver
{
    public $results = [];

    public $runResult = 1;
    public $addedMiddlewares = [];

    public static function for(array $results, $runResult)
    {
        $instance = new static();
        $instance->results = $results;
        $instance->runResult = $runResult;
        return $instance;
    }

    public function run(RawQuery $query)
    {
        return $this->runResult;
    }

    public function useMiddleware(string $step, array $middlewareList): Driver
    {
        $this->addedMiddlewares[$step] = $middlewareList;
        return $this;
    }

    public function appendMiddleware(string $step, callable $middleware): Driver
    {
        $this->addedMiddlewares[$step][] = $middleware;
        return $this;
    }

    public function fetchFirst(RawQuery $query)
    {
        return $this->results[0] ?? null;
    }

    public function fetchReader(RawQuery $query): ResultReader
    {
        return ResultReaderMock::create($this->results);
    }

    public function fetchAll(RawQuery $query): array
    {
        return $this->results;
    }

    public function fetch(RawQuery $query): ResultBuilder
    {
        $builder = new QueryResultBuilder();
        $builder->useReader($this->fetchReader($query));
        return $builder;
    }
}