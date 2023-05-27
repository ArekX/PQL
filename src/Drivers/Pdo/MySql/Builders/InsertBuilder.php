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
use ArekX\PQL\Drivers\Pdo\MySql\Builders\Traits\QuoteNameTrait;
use ArekX\PQL\Drivers\Pdo\MySql\Builders\Traits\SubQueryTrait;
use ArekX\PQL\Drivers\Pdo\MySql\Builders\Traits\WrapValueTrait;
use ArekX\PQL\Drivers\Pdo\MySql\MySqlQueryBuilderState;
use ArekX\PQL\Sql\Query\Insert;

/**
 * Represents a query builder for building an INSERT query
 *
 * @see Insert
 */
class InsertBuilder extends QueryPartBuilder
{
    use QuoteNameTrait;
    use WrapValueTrait;
    use SubQueryTrait;

    /**
     * @inheritDoc
     */
    protected function getInitialParts(): array
    {
        return ['INSERT'];
    }

    /**
     * @inheritDoc
     */
    protected function getRequiredParts(): array
    {
        return ['into', 'values'];
    }

    /**
     * @inheritDoc
     */
    protected function getPartBuilders(): array
    {
        return [
            'into' => fn($part, $state) => $this->buildInto($part, $state),
            'columns' => fn($part, $state) => $this->buildColumns($part, $state),
            'values' => fn($part, $state) => $this->buildValueItems($part, $state),
        ];
    }

    /**
     * Build INTO part.
     *
     * @param StructuredQuery|string $part Table name into the data to be inserted
     * @param MySqlQueryBuilderState $state Query builder state
     * @return string
     */
    protected function buildInto($part, $state)
    {
        if ($part instanceof StructuredQuery) {
            return 'INTO ' . $this->buildQuery($part, $state);
        }

        return 'INTO ' . $this->quoteName($part);
    }

    /**
     * Build specific columns which will be inserted.
     *
     * @param StructuredQuery|array $columns Columns to be inserted
     * @param MySqlQueryBuilderState $state Query builder state
     * @return string
     */
    protected function buildColumns($columns, $state)
    {
        if ($columns instanceof StructuredQuery) {
            return $this->buildQuery($columns, $state);
        }

        $result = [];

        foreach ($columns as $column) {
            $result[] = $this->quoteName($column);
        }

        return '(' . implode(', ', $result) . ')';
    }

    /**
     * Build multiple value items.
     *
     * @param array $valuesList List of values
     * @param MySqlQueryBuilderState $state Query builder state
     * @return string
     */
    protected function buildValueItems($valuesList, $state)
    {
        $results = [];

        foreach ($valuesList as $values) {
            $results[] = $this->buildValues($values, $state);
        }

        return 'VALUES ' . implode(' ', $results);
    }

    /**
     * Build specific values which will be inserted.
     *
     * @param StructuredQuery|array $values Values to be inserted
     * @param MySqlQueryBuilderState $state Query builder state
     * @return string
     */
    protected function buildValues($values, $state)
    {
        if ($values instanceof StructuredQuery) {
            return $this->buildQuery($values, $state);
        }

        $result = [];

        foreach ($values as $value) {
            $result[] = $this->buildWrapValue($value, $state);
        }

        return '(' . implode(', ', $result) . ')';
    }

    /**
     * @inheritDoc
     */
    protected function getLastParts(): array
    {
        return [];
    }
}
