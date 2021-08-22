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

class RawQuery implements Contracts\RawQuery
{
    public static function create($query, $params = null, $config = null)
    {
        return new static($query, $params, $config);
    }

    public function __construct($query, $params = null, $config = null)
    {

    }

    public function getQuery()
    {
        // TODO: Implement getQuery() method.
    }

    public function getParams(): array
    {
        // TODO: Implement getParams() method.
    }

    public function getConfig(): array
    {
        // TODO: Implement getConfig() method.
    }
}