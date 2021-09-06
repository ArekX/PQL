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

use ArekX\PQL\Drivers\MySql\Builder\MySqlQueryBuilderState;
use ArekX\PQL\Sql\Query\Raw;

/**
 * Represents a query builder for building raw queries.
 *
 * @see Raw
 */
class RawBuilder extends QueryPartBuilder
{
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
        return ['query'];
    }

    /**
     * @inheritDoc
     */
    protected function getPartBuilders(): array
    {
        return [
            'query' => fn($part) => $part,
            'params' => fn($part, MySqlQueryBuilderState $state) => $this->mergeParams($part, $state)
        ];
    }

    /**
     * Merges params set for this query into the parameters of the
     * currently passed state.
     *
     * @param array $params Parameters to be merged.
     * @param MySqlQueryBuilderState $state Query builder state.
     * @return null Null is always returned as this function only parses the parameters.
     */
    protected function mergeParams($params, MySqlQueryBuilderState $state)
    {
        $paramsBuilder = $state->getParamsBuilder();

        foreach ($params as $key => $value) {
            $type = null;
            $paramValue = $value;

            if (is_array($value)) {
                [$paramValue, $type] = $value;
            }

            $paramsBuilder->add($key, $paramValue, $type);
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    protected function getLastParts(): array
    {
        return [];
    }
}