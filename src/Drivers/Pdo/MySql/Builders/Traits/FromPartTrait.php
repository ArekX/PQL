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

namespace ArekX\PQL\Drivers\Pdo\MySql\Builders\Traits;

use ArekX\PQL\Contracts\StructuredQuery;
use ArekX\PQL\Drivers\Pdo\MySql\MySqlQueryBuilderState;

trait FromPartTrait
{
    use AliasTrait;

    /**
     * Build FROM query part.
     *
     * Names, sub queries and aliases are supported.
     *
     * @param string|array|StructuredQuery $part Part to be built
     * @param MySqlQueryBuilderState $state Query builder state
     * @return string Resulting query part.
     * @see AliasTrait::buildAliasedNames()
     */
    protected function buildFrom($part, MySqlQueryBuilderState $state)
    {
        return "FROM " . $this->buildAliasedNames($part, $state);
    }
}
