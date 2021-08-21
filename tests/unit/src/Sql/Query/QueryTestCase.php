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

namespace tests\src\Sql\Query;

use ArekX\PQL\Contracts\StructuredQuery;
use Codeception\Test\Unit;

class QueryTestCase extends Unit
{
    protected function assertQueryPartValues(StructuredQuery $query, string $part, array $values)
    {
        foreach ($values as $value) {
            $query->{$part}($value);
            expect($query->toArray()[$part])->toBe($value);
        }
    }

    protected function assertAppendedQueryPartValues(StructuredQuery $query, string $part, array $values)
    {
        $toCheck = [];
        foreach ($values as $value) {
            $query->{$part}($value);
            $toCheck[] = $value;
            expect($query->toArray()[$part])->toBe($toCheck);
        }
    }
}