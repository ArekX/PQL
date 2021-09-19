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

use ArekX\PQL\Contracts\QueryBuilder;
use ArekX\PQL\Contracts\QueryBuilderState;
use ArekX\PQL\Contracts\RawQuery;
use ArekX\PQL\Contracts\StructuredQuery;
use ArekX\PQL\RawQueryResult;

class QueryBuilderMock implements QueryBuilder
{
    public $query;
    public $params = null;
    public $config = null;

    public $lastState;

    public static function create()
    {
        return new static();
    }

    public function build(StructuredQuery $query, QueryBuilderState $state = null): RawQuery
    {
        $this->lastState = $state;
        return RawQueryResult::create($this->query, $this->params, $this->config);
    }
}