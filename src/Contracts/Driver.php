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

namespace ArekX\PQL\Contracts;

/**
 * Interface for a driver to run the raw queries.
 */
interface Driver
{
    public function run(RawQuery $query);

    public function useMiddleware(string $step, array $middlewareList): self;

    public function appendMiddleware(string $step, callable $middleware): self;

    public function fetchFirst(RawQuery $query);

    public function fetchReader(RawQuery $query): ResultReader;

    public function fetchAll(RawQuery $query): array;

    public function fetch(RawQuery $query): ResultBuilder;
}