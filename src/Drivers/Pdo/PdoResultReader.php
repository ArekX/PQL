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

namespace ArekX\PQL\Drivers\Pdo;

use ArekX\PQL\Contracts\ResultReader;

class PdoResultReader implements ResultReader
{
    public \PDOStatement $statement;
    public $fetchMode = \PDO::FETCH_ASSOC;

    public static function createFromStatement(\PDOStatement $statement)
    {
        return new static($statement);
    }

    public function __construct(\PDOStatement $statement)
    {
        $this->statement = $statement;
    }

    public function getAllRows()
    {
        $results = [];

        while(($row = $this->getNextRow()) !== false) {
            $results[] = $row;
        }

        return $results;
    }

    public function getAllColumns($columnIndex = 0)
    {
        $results = [];

        while(($column = $this->getNextColumn($columnIndex)) !== false) {
            $results[] = $column;
        }

        return $results;
    }

    public function getNextRow()
    {
        return $this->statement->fetch($this->fetchMode);
    }

    public function getNextColumn($columnIndex = 0)
    {
        return $this->statement->fetchColumn($columnIndex);
    }

    public function finalize()
    {
        $this->statement->closeCursor();
    }

    public function reset()
    {
        $this->statement->execute();
    }
}