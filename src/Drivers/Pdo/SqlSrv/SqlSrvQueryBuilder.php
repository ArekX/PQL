<?php

/**
 * Copyright 2026 Aleksandar Panic
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

namespace ArekX\PQL\Drivers\Pdo\SqlSrv;

use ArekX\PQL\Drivers\Pdo\Common\CommonQueryBuilder;
use ArekX\PQL\Drivers\Pdo\SqlSrv\Builders\SqlSrvSelectBuilder;
use ArekX\PQL\Sql\Query\Select;

/**
 * Represents a query builder for Microsoft SQL Server.
 *
 * It reuses the shared builders from the Common namespace configured for the
 * SQL Server dialect: identifiers are quoted with square brackets and SELECT
 * uses TOP / OFFSET FETCH instead of LIMIT/OFFSET.
 */
class SqlSrvQueryBuilder extends CommonQueryBuilder
{
    /**
     * @inheritDoc
     */
    protected const QUOTE_CHARACTER = '[';

    /**
     * @inheritDoc
     */
    protected const CLOSING_QUOTE_CHARACTER = ']';

    /**
     * @inheritDoc
     */
    protected const SUPPORTS_MODIFY_LIMIT = false;

    public function __construct()
    {
        // SQL Server needs its own SELECT builder for TOP / OFFSET FETCH.
        $this->builderMap[Select::class] = SqlSrvSelectBuilder::class;
    }
}
