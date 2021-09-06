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

namespace ArekX\PQL\Drivers\MySql\Builder\Builders;

use ArekX\PQL\Contracts\StructuredQuery;
use ArekX\PQL\Drivers\MySql\Builder\Builders\Traits\ConditionTrait;
use ArekX\PQL\Drivers\MySql\Builder\Builders\Traits\ValueColumnTrait;
use ArekX\PQL\Drivers\MySql\Builder\MySqlQueryBuilderState;
use ArekX\PQL\Sql\Statement\CaseWhen;

/**
 * Represents a query builder for building a CASE statement
 *
 * @see CaseWhen
 */
class CaseWhenBuilder extends QueryPartBuilder
{
    use ConditionTrait;
    use ValueColumnTrait;

    /**
     * @inheritDoc
     */
    protected function getInitialParts(): array
    {
        return ['CASE'];
    }

    /**
     * @inheritDoc
     */
    protected function getLastParts(): array
    {
        return ['END'];
    }

    /**
     * @inheritDoc
     */
    protected function getRequiredParts(): array
    {
        return ['when'];
    }

    /**
     * @inheritDoc
     */
    protected function getPartBuilders(): array
    {
        return [
            'when' => fn($name, MySqlQueryBuilderState $state) => $this->buildWhen($name, $state),
            'else' => fn($name, MySqlQueryBuilderState $state) => $this->buildElse($name, $state),
        ];
    }

    /**
     * Build series of WHEN parts of the CASE.
     *
     * @param array $whenList List of [when, then] values to be built.
     * @param MySqlQueryBuilderState $state
     * @return string
     * @throws \Exception
     */
    protected function buildWhen($whenList, MySqlQueryBuilderState $state)
    {
        $result = [];

        foreach ($whenList as $whenItem) {
            [$when, $then] = $whenItem;

            $result[] = 'WHEN ' . $this->buildCondition($when, $state) . ' THEN ' . $this->buildValueColumn($then, $state);
        }

        return implode(' ', $result);
    }

    /**
     * Build ELSE part of the case
     *
     * @param array|StructuredQuery $else Value to be built
     * @param MySqlQueryBuilderState $state Current query builder state
     * @return string
     * @throws \Exception
     */
    protected function buildElse($else, MySqlQueryBuilderState $state)
    {
        return 'ELSE ' . $this->buildValueColumn($else, $state);
    }
}