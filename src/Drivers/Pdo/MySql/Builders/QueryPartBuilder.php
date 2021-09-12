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

use ArekX\PQL\Contracts\QueryBuilder;
use ArekX\PQL\Contracts\QueryBuilderState;
use ArekX\PQL\Contracts\RawQuery;
use ArekX\PQL\Contracts\StructuredQuery;
use ArekX\PQL\Drivers\Pdo\MySql\MySqlQueryBuilderState;
use ArekX\PQL\RawQueryResult;

/**
 * Represents a base builder for query parts.
 */
abstract class QueryPartBuilder implements QueryBuilder
{
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


        return RawQueryResult::create(
            $this->joinParts($state->getQueryPartGlue(), $this->buildQueryParts($input, $state)),
            $state->getParamsBuilder()->build(),
            $query->get('config') ?? null
        );
    }

    /**
     * Join built parts into one complete string.
     *
     * Part can be a string, in which case it will be joined with other by
     * using a string in $stringGlue
     *
     * If a part is an array it is used as a tuple [glue, partString] and
     * it will use its own glue string to join.
     *
     * @param string $stringGlue Default string glue to use if part is string
     * @param array $builtParts List of parts to join.
     *
     * @return string
     */
    protected function joinParts($stringGlue, array $builtParts): string
    {
        $result = '';

        foreach ($builtParts as $index => $part) {
            if (is_string($part)) {
                $result .= $index > 0 ? $stringGlue . $part : $part;
                continue;
            }

            [$partGlue, $partString] = $part;
            $result .= $partGlue . $partString;
        }

        return $result;
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

            $result = $buildPart($parts[$partName], $state);

            if ($result !== null) {
                $results[] = $result;
            }
        }

        foreach ($this->getLastParts() as $part) {
            $results[] = $part;
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

    /**
     * Return last parts to be applied at the end of the query.
     *
     * @return array
     */
    protected abstract function getLastParts(): array;
}