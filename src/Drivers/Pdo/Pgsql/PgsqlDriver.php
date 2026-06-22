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

use ArekX\PQL\Drivers\Pdo\PdoDriver;

/**
 * Driver for handling connections for
 * a PostgreSQL driver.
 */
class PgsqlDriver extends PdoDriver
{
    /**
     * Whether to emulate prepared statements.
     *
     * Null (the default) leaves the PDO driver on its own default, which for
     * PostgreSQL is real (server side) prepared statements. Set it to true or
     * false to force that setting.
     *
     * @var bool|null
     */
    public ?bool $emulatePrepare = null;

    /**
     * What character set to use during the connection.
     *
     * @var string|null
     */
    public ?string $charset = null;

    /**
     * @inheritDoc
     */
    protected function createPdoInstance(): \PDO
    {
        $instance = new \PDO($this->dsn, $this->username, $this->password, $this->options);

        // Only apply when explicitly set. Comparing against null (instead of a
        // truthy check) is what lets emulatePrepare = true actually enable
        // emulation; leaving it null keeps PDO on its own default.
        if ($this->emulatePrepare !== null) {
            $instance->setAttribute(\PDO::ATTR_EMULATE_PREPARES, $this->emulatePrepare);
        }

        if ($this->charset) {
            $instance->exec('SET NAMES ' . $instance->quote($this->charset));
        }

        return $instance;
    }


    /**
     * Extends with additional config
     *
     * Format is:
     * ```php
     * [
     *    'emulatePrepare' => false,
     *    'charset' => 'UTF8'
     * ]
     * ```
     *
     * @param array $config
     * @return void
     */
    protected function extendConfigure(array $config): void
    {
        $this->emulatePrepare = $config['emulatePrepare'] ?? $this->emulatePrepare;
        $this->charset = $config['charset'] ?? $this->charset;
    }
}
