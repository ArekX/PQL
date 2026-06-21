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

namespace ArekX\PQL\Drivers\Pdo\Pgsql;

use ArekX\PQL\Drivers\Pdo\Common\CommonQueryBuilder;

/**
 * Represents a query builder for PostgreSQL.
 *
 * It reuses the shared builders from the Common namespace configured for the
 * PostgreSQL dialect: identifiers are quoted with double quotes and LIMIT/OFFSET
 * are not allowed on UPDATE and DELETE statements.
 */
class PgsqlQueryBuilder extends CommonQueryBuilder
{
    /**
     * @inheritDoc
     */
    protected const QUOTE_CHARACTER = '"';

    /**
     * @inheritDoc
     */
    protected const SUPPORTS_MODIFY_LIMIT = false;
}
