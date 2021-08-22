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

use ArekX\PQL\Contracts\QueryBuilder;
use ArekX\PQL\Contracts\QueryBuilderState;
use ArekX\PQL\Contracts\RawQuery;
use ArekX\PQL\Contracts\StructuredQuery;
use ArekX\PQL\Drivers\MySql\Builder\MySqlQueryBuilderState;
use ArekX\PQL\RawQueryResult;

/**
 * Represents a base builder for query parts.
 */
abstract class QueryPartBuilder implements QueryBuilder
{
    /**
     * Build a specific query part.
     *
     * If the part is a structured query it will be built using a parent query builder.
     *
     * @param array|StructuredQuery $part Part to be built
     * @param callable $buildPart Part builder
     * @param MySqlQueryBuilderState $state State to be passed to the builder.
     * @return mixed Result of the query.
     */
    protected function buildPart($part, $buildPart, MySqlQueryBuilderState $state)
    {
        if ($part instanceof StructuredQuery) {
            return $state->getParentBuilder()->build($part, $state)->getQuery();
        }

        return $buildPart($part, $state);
    }

    /**
     * @inheritDoc
     */
    public function build(StructuredQuery $query, QueryBuilderState $state = null): RawQuery
    {
        /** @var MySqlQueryBuilderState $state */

        if ($state === null) {
            throw new \Exception('Passed state cannot be null.');
        }

        $input = $query->toArray();
        $results = implode($state->getQueryPartGlue(), $this->buildQueryParts($input, $state));

        return RawQueryResult::create(
            $results,
            $state->getParamsBuilder()->build(),
            $query->get('config') ?? null
        );
    }

    /**
     * Build each part from the structured query
     *
     * @param array $parts Parts from the structured query
     * @param MySqlQueryBuilderState $state Builder state
     * @return array Resulting array for each built part.
     * @throws \Exception
     */
    protected function buildQueryParts(array $parts, MySqlQueryBuilderState $state): array
    {
        $results = $this->getInitialParts();
        $requiredParts = $this->getRequiredParts();

        foreach ($this->getPartBuilders() as $partName => $buildPart) {
            if (empty($parts[$partName])) {

                if (in_array($partName, $requiredParts)) {
                    throw new \Exception("Part '${partName}' is required.");
                }

                continue;
            }

            $result = $this->buildPart($parts[$partName], $buildPart, $state);

            if ($result !== null) {
                $results[] = $result;
            }
        }

        return $results;
    }

    /**
     * Return initial parts for the query
     *
     * @return array
     */
    protected abstract function getInitialParts(): array;

    /**
     * Return parts which must be set in the query in order to be built.
     * @return array
     */
    protected abstract function getRequiredParts(): array;

    /**
     * Return part builders for resolving the query parts to string.
     *
     * @return array
     */
    protected abstract function getPartBuilders(): array;
}