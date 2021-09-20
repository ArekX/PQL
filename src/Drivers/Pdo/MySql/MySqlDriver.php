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

class MySqlDriver extends PdoDriver
{
    public $emulatePrepare;

    public $charset;

    protected function createPdoInstance(): \PDO
    {
        $instance = new \PDO($this->dsn, $this->username, $this->password, $this->options);

        if ($this->emulatePrepare !== null) {
            $instance->setAttribute(\PDO::ATTR_EMULATE_PREPARES, $this->emulatePrepare);
        }

        if ($this->charset) {
            $pdo = $this->getPdo();
            $pdo->exec('SET NAMES ' . $pdo->quote($this->charset));
        }

        return $instance;
    }

    protected function extendConfigure(array $config)
    {
        $this->emulatePrepare = $config['emulatePrepare'] ?? $this->emulatePrepare;
        $this->charset = $config['charset'] ?? $this->charset;
    }
}