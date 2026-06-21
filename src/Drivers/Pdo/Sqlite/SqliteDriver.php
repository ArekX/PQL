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

namespace ArekX\PQL\Drivers\Pdo\Sqlite;

use ArekX\PQL\Drivers\Pdo\PdoDriver;
use PDO;

/**
 * Driver for handling connections for a SQLite database.
 */
class SqliteDriver extends PdoDriver
{
    /**
     * SQLite has no authentication, so username/password are unused. They
     * default to empty strings because the base PDO driver passes them to the
     * PDO constructor.
     *
     * @var string
     */
    public string $username = '';

    /**
     * @var string
     */
    public string $password = '';

    /**
     * Whether to enable foreign key constraint enforcement.
     *
     * SQLite has foreign key enforcement turned off by default, so this is
     * false unless explicitly enabled.
     *
     * @var bool
     */
    public bool $foreignKeys = false;

    /**
     * @inheritDoc
     */
    protected function createPdoInstance(): PDO
    {
        $instance = new PDO($this->dsn, $this->username, $this->password, $this->options);

        if ($this->foreignKeys) {
            $instance->exec('PRAGMA foreign_keys = ON');
        }

        return $instance;
    }

    /**
     * Extends with additional config
     *
     * Format is:
     * ```php
     * [
     *    'foreignKeys' => false
     * ]
     * ```
     *
     * @param array $config
     * @return void
     */
    protected function extendConfigure(array $config): void
    {
        $this->foreignKeys = $config['foreignKeys'] ?? $this->foreignKeys;
    }
}
