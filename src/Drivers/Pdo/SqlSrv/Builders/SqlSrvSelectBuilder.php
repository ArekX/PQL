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

namespace ArekX\PQL\Drivers\Pdo\SqlSrv\Builders;

use ArekX\PQL\Drivers\Pdo\Common\Builders\SelectBuilder;
use ArekX\PQL\Drivers\Pdo\Common\CommonQueryBuilderState;

/**
 * SELECT builder for SQL Server.
 *
 * SQL Server does not support the LIMIT/OFFSET syntax. Instead it uses:
 *  - `SELECT TOP (n) ...` when only a limit is set, and
 *  - `OFFSET m ROWS FETCH NEXT n ROWS ONLY` when an offset is set.
 *
 * Note: the OFFSET/FETCH form requires the query to have an ORDER BY clause, as
 * mandated by SQL Server.
 *
 * @see SelectBuilder
 */
class SqlSrvSelectBuilder extends SelectBuilder
{
    /**
     * @inheritDoc
     */
    protected function getPartBuilders(): array
    {
        $parts = parent::getPartBuilders();

        // LIMIT/OFFSET are rendered separately for SQL Server.
        unset($parts['limit'], $parts['offset']);

        return $parts;
    }

    /**
     * @inheritDoc
     */
    protected function buildQueryParts(array $parts, CommonQueryBuilderState $state): array
    {
        $results = parent::buildQueryParts($parts, $state);

        $limit = $parts['limit'] ?? null;
        $offset = $parts['offset'] ?? null;

        if ($offset !== null) {
            $clause = 'OFFSET ' . (int)$offset . ' ROWS';

            if ($limit !== null) {
                $clause .= ' FETCH NEXT ' . (int)$limit . ' ROWS ONLY';
            }

            $results[] = $clause;
        } elseif ($limit !== null) {
            // Inject TOP right after the SELECT keyword (the first part).
            $results[0] = 'SELECT TOP (' . (int)$limit . ')';
        }

        return $results;
    }
}
