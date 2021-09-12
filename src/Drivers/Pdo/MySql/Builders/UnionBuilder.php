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

namespace ArekX\PQL\Drivers\Pdo\MySql\Builders;

use ArekX\PQL\Drivers\Pdo\MySql\Builders\Traits\SubQueryTrait;
use ArekX\PQL\Drivers\Pdo\MySql\MySqlQueryBuilderState;
use ArekX\PQL\Sql\Query\Union;

/**
 * Represents a query builder for building an UNION query
 *
 * @see Union
 */
class UnionBuilder extends QueryPartBuilder
{
    use SubQueryTrait;

    /**
     * @inheritDoc
     */
    protected function getInitialParts(): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    protected function getRequiredParts(): array
    {
        return ['from'];
    }

    /**
     * @inheritDoc
     */
    protected function getPartBuilders(): array
    {
        return [
            'from' => fn($part, $state) => $this->buildQuery($part, $state),
            'union' => fn($part, $state) => $this->buildUnions($part, $state)
        ];
    }

    /**
     * Build other unions added.
     *
     * @param array $unions Unions to be built
     * @param MySqlQueryBuilderState $state Query builder state
     * @return string
     */
    protected function buildUnions($unions, $state)
    {
        $result = [];
        foreach ($unions as [$query, $type]) {
            $result[] = ($type ? strtoupper($type) . ' ' : '') . $this->buildQuery($query, $state);
        }

        return 'UNION ' . implode(' UNION ', $result);
    }

    /**
     * @inheritDoc
     */
    protected function getLastParts(): array
    {
        return [];
    }
}