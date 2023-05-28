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

use Exception;

/**
 * Transaction for handling PDO connections.
 *
 */
class PdoTransaction
{
    /**
     * Driver to be used for transaction.
     *
     * @var PdoDriver
     */
    protected PdoDriver $driver;

    /**
     * Whether the transaction is finalized.
     * @var bool
     */
    protected bool $isFinalized = false;

    /**
     * Creates a new transaction from the driver.
     *
     * @param PdoDriver $driver Driver to be used
     * @return static
     */
    public static function create(PdoDriver $driver): static
    {
        $instance = new static();
        $instance->driver = $driver;

        $instance->driver->getPdo()->beginTransaction();

        return $instance;
    }


    /**
     * Executes a method and commits the result
     * if method throws an exception, transaction
     * is rolled back.
     *
     * @param callable $method Method to be executed.
     * @return mixed Result from the methods
     * @throws Exception
     */
    public function execute(callable $method): mixed
    {
        try {
            $result = $method($this);
            $this->commit();
            return $result;
        } catch (Exception $e) {
            $this->rollback();
            throw $e;
        }
    }

    /**
     * Commits all queries executed on the driver since this
     * transaction started.
     *
     * If this or rollback method is called before this method, subsequent
     * calls are ignored.
     *
     * @return void
     */
    public function commit(): void
    {
        if ($this->isFinalized) {
            return;
        }

        $this->driver->getPdo()->commit();
        $this->isFinalized = true;
    }

    /**
     * Rollbacks all queries executed on this driver since
     * this transaction started.
     *
     * If this or rollback method is called before this method, subsequent
     * calls are ignored.
     *
     * @return void
     */
    public function rollback(): void
    {
        if ($this->isFinalized) {
            return;
        }

        $this->driver->getPdo()->rollBack();
        $this->isFinalized = true;
    }
}
