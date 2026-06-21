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

use ArekX\PQL\Drivers\Pdo\PdoDriver;
use PDO;

/**
 * Driver for handling connections for a Microsoft SQL Server database.
 *
 * Works with either the `sqlsrv` PDO driver (Microsoft) or the `dblib` PDO
 * driver (FreeTDS); the connection style is selected through the DSN, e.g.
 * `sqlsrv:Server=127.0.0.1,1433;Database=test` or
 * `dblib:host=127.0.0.1:1433;dbname=test`.
 */
class SqlSrvDriver extends PdoDriver
{
    /**
     * @inheritDoc
     */
    protected function createPdoInstance(): PDO
    {
        return new PDO($this->dsn, $this->username, $this->password, $this->options);
    }

    /**
     * @inheritDoc
     */
    protected function extendConfigure(array $config): void
    {
        // No SQL Server specific configuration.
    }
}
