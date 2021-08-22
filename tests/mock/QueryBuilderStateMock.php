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

use ArekX\PQL\Contracts\QueryBuilderState;

class QueryBuilderStateMock implements QueryBuilderState
{
    public $state = [];

    public function get(string $name, $default = null)
    {
        return $this->state[$name] ?? $default;
    }

    public function set(string $name, $value): void
    {
        $this->state[$name] = $value;
    }
}