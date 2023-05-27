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

/**
 * PDO Reader for reading data from Statements.
 */
class PdoResultReader implements ResultReader
{
    /**
     * Current statement in use.
     * @var \PDOStatement
     */
    public \PDOStatement $statement;

    /**
     * Current fetch mode.
     * @var int
     */
    public int $fetchMode = \PDO::FETCH_ASSOC;

    /**
     * Whether statement was executed.
     * @var bool
     */
    protected bool $isExecuted = false;

    /**
     * Constructor for PDO Result reader
     * @param \PDOStatement $statement Statement to be read from.
     */
    public function __construct(\PDOStatement $statement)
    {
        $this->statement = $statement;
    }


    /**
     * Create a reader from PDO statement.
     *
     * @param \PDOStatement $statement
     * @return static
     */
    public static function create(\PDOStatement $statement)
    {
        return new static($statement);
    }

    /**
     * @inheritDoc
     */
    public function getAllRows(): array
    {
        $results = [];

        while (($row = $this->getNextRow()) !== false) {
            $results[] = $row;
        }

        return $results;
    }

    /**
     * @inheritDoc
     */
    public function getNextRow(): mixed
    {
        return $this->statement->fetch($this->fetchMode);
    }

    /**
     * @inheritDoc
     */
    public function getAllColumns($columnIndex = 0): array
    {
        $results = [];

        while (($column = $this->getNextColumn($columnIndex)) !== false) {
            $results[] = $column;
        }

        return $results;
    }

    /**
     * @inheritDoc
     */
    public function getNextColumn($columnIndex = 0): mixed
    {
        return $this->statement->fetchColumn($columnIndex);
    }

    /**
     * @inheritDoc
     */
    public function reset(): void
    {
        if ($this->isExecuted) {
            $this->finalize();
        }

        $this->statement->execute();
        $this->isExecuted = true;
    }

    /**
     * @inheritDoc
     */
    public function finalize(): void
    {
        $this->statement->closeCursor();
        $this->isExecuted = false;
    }
}
