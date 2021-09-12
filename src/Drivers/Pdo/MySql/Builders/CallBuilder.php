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
use ArekX\PQL\Drivers\Pdo\MySql\Builders\Traits\ValueColumnTrait;
use ArekX\PQL\Drivers\Pdo\MySql\MySqlQueryBuilderState;
use ArekX\PQL\Sql\Statement\Call;

/**
 * Represents a query builder for building a stored procedure call.
 *
 * @see Call
 */
class CallBuilder extends QueryPartBuilder
{
    use ValueColumnTrait;

    /**
     * @inheritDoc
     */
    protected function getInitialParts(): array
    {
        return ['CALL'];
    }

    /**
     * @inheritDoc
     */
    protected function getRequiredParts(): array
    {
        return ['name'];
    }

    /**
     * @inheritDoc
     */
    protected function getPartBuilders(): array
    {
        return [
            'name' => fn($name, MySqlQueryBuilderState $state) => $this->buildNamePart($name, $state),
            'params' => fn($params, MySqlQueryBuilderState $state) => ['', $this->buildParamsPart($params, $state)],
        ];
    }

    /**
     * Build name of the procedure part.
     *
     * @param string $name Name of the procedure
     * @param MySqlQueryBuilderState $state Current query builder state.
     * @return string
     */
    protected function buildNamePart($name, MySqlQueryBuilderState $state)
    {
        if ($name instanceof StructuredQuery) {
            return $this->buildQuery($name, $state);
        }

        return $name;
    }

    /**
     * Build procedure parameters part.
     *
     * @param array $params Procedure parameters to be built.
     * @param MySqlQueryBuilderState $state Current query builder state.
     * @return string
     *
     * @throws \Exception
     */
    protected function buildParamsPart($params, MySqlQueryBuilderState $state)
    {
        $result = [];

        foreach ($params as $param) {
            $result[] = $this->buildValueColumn($param, $state);
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