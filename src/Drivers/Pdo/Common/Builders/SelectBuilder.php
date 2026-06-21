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

namespace ArekX\PQL\Drivers\Pdo\Common\Builders;

use ArekX\PQL\Contracts\StructuredQuery;
use ArekX\PQL\Drivers\Pdo\Common\Builders\Traits\AliasTrait;
use ArekX\PQL\Drivers\Pdo\Common\Builders\Traits\FromPartTrait;
use ArekX\PQL\Drivers\Pdo\Common\Builders\Traits\JoinTrait;
use ArekX\PQL\Drivers\Pdo\Common\Builders\Traits\NumberPartTrait;
use ArekX\PQL\Drivers\Pdo\Common\Builders\Traits\WhereTrait;
use ArekX\PQL\Drivers\Pdo\Common\CommonQueryBuilderState;
use ArekX\PQL\Sql\Query\Select;

/**
 * Represents a query builder for building a SELECT query.
 *
 * @see Select
 */
class SelectBuilder extends QueryPartBuilder
{
    use AliasTrait;
    use FromPartTrait;
    use WhereTrait;
    use NumberPartTrait;
    use JoinTrait;

    /**
     * @inheritDoc
     */
    protected function getInitialParts(): array
    {
        return ['SELECT'];
    }

    /**
     * @inheritDoc
     */
    protected function getRequiredParts(): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    protected function getPartBuilders(): array
    {
        return [
            'columns' => fn($part, $state) => $this->buildAliasedNames($part, $state),
            'from' => fn($part, $state) => $this->buildFrom($part, $state),
            'join' => fn($part, $state) => $this->buildJoin($part, $state),
            'where' => fn($part, $state) => $this->buildWhere($part, $state),
            'groupBy' => fn($part, $state) => $this->buildGroupBy($part, $state),
            'having' => fn($part, $state) => $this->buildHaving($part, $state),
            'orderBy' => fn($part, $state) => $this->buildOrderBy($part, $state),
            'limit' => fn($part) => $this->buildLimit($part),
            'offset' => fn($part) => $this->buildOffset($part),
        ];
    }

    /**
     * Build GROUP BY part
     *
     * @param array|StructuredQuery $groupBy Part to be build
     * @param CommonQueryBuilderState $state Query builder state
     * @return string
     */
    protected function buildGroupBy(StructuredQuery|array $groupBy, CommonQueryBuilderState $state): string
    {
        if ($groupBy instanceof StructuredQuery) {
            return 'GROUP BY ' . $this->buildQuery($groupBy, $state);
        }

        $result = [];
        foreach ($groupBy as $name) {
            $result[] = $this->quoteName($name, $state);
        }

        return 'GROUP BY ' . implode(', ', $result);
    }

    /**
     * Build HAVING part
     *
     * @param array|StructuredQuery $condition Condition to be built.
     * @param CommonQueryBuilderState $state Query builder state
     * @return string
     * @throws \Exception
     */
    protected function buildHaving(StructuredQuery|array $condition, CommonQueryBuilderState $state): string
    {
        return 'HAVING ' . $this->buildCondition($condition, $state);
    }

    /**
     * Build ORDER BY part
     *
     * @param array|StructuredQuery $orders Part to be built
     * @param CommonQueryBuilderState $state Query builder state
     * @return string
     */
    protected function buildOrderBy(StructuredQuery|array $orders, CommonQueryBuilderState $state): string
    {
        if ($orders instanceof StructuredQuery) {
            return 'ORDER BY '. $this->buildQuery($orders, $state);
        }

        $result = [];
        foreach ($orders as $by => $order) {
            if (!is_string($order)) {
                $order = $order === SORT_ASC ? 'asc' : 'desc';
            }

            $result[] = $this->quoteName($by, $state) . ' ' . strtoupper($order);
        }

        return 'ORDER BY ' . implode(', ', $result);
    }

    /**
     * @inheritDoc
     */
    protected function getLastParts(): array
    {
        return [];
    }
}
