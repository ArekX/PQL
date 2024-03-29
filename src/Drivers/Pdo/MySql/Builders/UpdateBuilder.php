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

use ArekX\PQL\Contracts\StructuredQuery;
use ArekX\PQL\Drivers\Pdo\MySql\Builders\Traits\JoinTrait;
use ArekX\PQL\Drivers\Pdo\MySql\Builders\Traits\NumberPartTrait;
use ArekX\PQL\Drivers\Pdo\MySql\Builders\Traits\QuoteNameTrait;
use ArekX\PQL\Drivers\Pdo\MySql\Builders\Traits\SubQueryTrait;
use ArekX\PQL\Drivers\Pdo\MySql\Builders\Traits\WhereTrait;
use ArekX\PQL\Drivers\Pdo\MySql\Builders\Traits\WrapValueTrait;
use ArekX\PQL\Drivers\Pdo\MySql\MySqlQueryBuilderState;
use ArekX\PQL\Sql\Query\Update;

/**
 * Represents a query builder for building UPDATE query.
 *
 * @see Update
 */
class UpdateBuilder extends QueryPartBuilder
{
    use QuoteNameTrait;
    use WrapValueTrait;
    use SubQueryTrait;
    use JoinTrait;
    use WhereTrait;
    use NumberPartTrait;

    /**
     * @inheritDoc
     */
    protected function getInitialParts(): array
    {
        return ['UPDATE'];
    }

    /**
     * @inheritDoc
     */
    protected function getRequiredParts(): array
    {
        return ['to', 'set'];
    }

    /**
     * @inheritDoc
     */
    protected function getPartBuilders(): array
    {
        return [
            'to' => fn($parts, $state) => $this->buildTo($parts, $state),
            'join' => fn($parts, $state) => $this->buildJoin($parts, $state),
            'set' => fn($parts, $state) => $this->buildSet($parts, $state),
            'where' => fn($parts, $state) => $this->buildWhere($parts, $state),
            'limit' => fn($parts) => $this->buildLimit($parts),
            'offset' => fn($parts) => $this->buildOffset($parts),
        ];
    }

    /**
     * Builds table which will be updated.
     *
     * @param string|StructuredQuery $parts Part which will be updated.
     * @param MySqlQueryBuilderState $state Query builder state.
     * @return string
     */
    protected function buildTo(StructuredQuery|string $parts, MySqlQueryBuilderState $state): string
    {
        if ($parts instanceof StructuredQuery) {
            return $this->buildQuery($parts, $state);
        }

        return $this->quoteName($parts);
    }

    /**
     * Build SET part.
     *
     * @param array|StructuredQuery $parts Part to be built
     * @param MySqlQueryBuilderState $state Query builder state
     * @return string
     */
    protected function buildSet(StructuredQuery|array $parts, MySqlQueryBuilderState $state): string
    {
        if ($parts instanceof StructuredQuery) {
            return 'SET ' . $this->buildQuery($parts, $state);
        }

        $results = [];

        foreach ($parts as $key => $value) {
            $results[] = $this->quoteName($key) . ' = ' . $this->buildWrapValue($value, $state);
        }

        return 'SET ' . implode(', ', $results);
    }

    /**
     * @inheritDoc
     */
    protected function getLastParts(): array
    {
        return [];
    }
}
