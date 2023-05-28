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

namespace ArekX\PQL\Drivers\Pdo\MySql;

use ArekX\PQL\Drivers\Pdo\PdoDriver;

/**
 * Driver for handling connections for
 * a MySQL driver.
 */
class MySqlDriver extends PdoDriver
{
    /**
     * Whether to emulate prepare statement.
     *
     * @var bool
     */
    public bool $emulatePrepare = false;

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

        if ($this->emulatePrepare) {
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
     *    'charset' => 'utf8mb4'
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
