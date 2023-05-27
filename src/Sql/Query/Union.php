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

namespace ArekX\PQL\Sql\Query;

use ArekX\PQL\Contracts\GroupedSubQuery;
use ArekX\PQL\Contracts\StructuredQuery;
use ArekX\PQL\Query;
use ArekX\PQL\Sql\Query\Traits\ConfigureTrait;

/**
 * Represents an union query
 * for union of multiple queries.
 */
class Union extends Query implements GroupedSubQuery
{
    use ConfigureTrait;

    /**
     * Set initial query to union other
     * queries with.
     *
     * @param StructuredQuery $from Query to be used.
     * @return $this
     */
    public function from(StructuredQuery $from): static
    {
        $this->use('from', $from);
        return $this;
    }

    /**
     * Append union to the current
     *
     * @param StructuredQuery $query Query to be used.
     * @param string|null $type Type of the union. If null, means no specific type.
     * @return $this
     */
    public function unionWith(StructuredQuery $query, string $type = null): static
    {
        $this->append('union', [$query, $type]);
        return $this;
    }
}
