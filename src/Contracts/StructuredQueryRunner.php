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

interface StructuredQueryRunner
{
    public function useDriver(Driver $driver): self;

    public function useBuilder(QueryBuilder $builder): self;

    public function run(StructuredQuery $query);

    public function fetchFirst(StructuredQuery $query);

    public function fetchAll(StructuredQuery $query): array;

    public function fetch(StructuredQuery $query): ResultBuilder;
}