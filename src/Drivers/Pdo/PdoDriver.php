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

use ArekX\PQL\Contracts\Driver;
use ArekX\PQL\Contracts\RawQuery;
use ArekX\PQL\Contracts\ResultBuilder;
use ArekX\PQL\Contracts\ResultReader;
use ArekX\PQL\QueryResultBuilder;

abstract class PdoDriver implements Driver
{
    const STEP_OPEN = 'open';
    const STEP_CLOSE = 'close';
    const STEP_BEFORE_RUN = 'before-run';
    const STEP_BEFORE_PREPARE = 'before-prepare';
    const STEP_AFTER_PREPARE = 'after-prepare';
    const STEP_AFTER_RUN = 'after-run';

    public $fetchMode = \PDO::FETCH_ASSOC;
    public $dsn;
    public $username;
    public $password;
    public $options = [];

    protected $middlewares = [];
    protected ?\PDO $pdo;

    public static function create($config = [])
    {
        $instance = new static();
        $instance->configure($config);

        return $instance;
    }

    public function configure(array $config)
    {
        $this->fetchMode ??= $config['fetchMode'];
        $this->dsn ??= $config['dsn'];
        $this->username ??= $config['username'];
        $this->password ??= $config['password'];
        $this->options ??= $config['options'];

        $middlewares = $config['middleware'] ?? [];

        foreach ($middlewares as $step => $middlewareList) {
            $this->useMiddleware($step, $middlewareList);
        }

        $this->extendConfigure($config);
    }

    public function useMiddleware(string $step, array $middlewareList): Driver
    {
        $this->middlewares[$step] = array_values($middlewareList);
        return $this;
    }

    protected abstract function extendConfigure(array $config);

    public function close()
    {
        if ($this->pdo === null) {
            return;
        }

        $this->pdo = null;
        $this->runMiddleware(self::STEP_CLOSE);
    }

    protected function runMiddleware($step, $result = null, ...$params)
    {
        if (empty($this->middlewares[$step])) {
            return $result;
        }

        return $this->executeMiddlewareChain($step, 0, $result, $params);
    }

    protected function executeMiddlewareChain($step, $index, $result, $params)
    {
        if (empty($this->middlewares[$step][$index])) {
            return $result;
        }

        return $this->middlewares[$step][$index](
            $result,
            $params,
            fn($result) => $this->executeMiddlewareChain($step, $index + 1, $result, $params)
        );
    }

    public function runInTransaction(callable $transactionRunner)
    {
        $transaction = $this->beginTransaction();

        try {
            $result = $transactionRunner($this);
            $transaction->commit();
            return $result;
        } catch (\Exception $e) {
            $transaction->rollback();
            throw $e;
        }
    }

    public function beginTransaction(): PdoTransaction
    {
        return PdoTransaction::create($this);
    }

    public function getLastInsertedId($sequenceName = null)
    {
        return $this->getPdo()->lastInsertId($sequenceName);
    }

    public function getPdo(): \PDO
    {
        $this->open();
        return $this->pdo;
    }

    public function open()
    {
        if ($this->pdo) {
            return;
        }

        $this->pdo = $this->createPdoInstance();
        $this->runMiddleware(self::STEP_OPEN, $this->pdo);
    }

    protected abstract function createPdoInstance(): \PDO;

    public function run(RawQuery $query)
    {
        $query = $this->runMiddleware(self::STEP_BEFORE_RUN, $query, 'run');

        $statement = $this->executeStatement($query);
        $affected = $statement->rowCount();
        $statement->closeCursor();

        return $this->runMiddleware(self::STEP_AFTER_RUN, $affected, $query, 'run');
    }

    protected function executeStatement(RawQuery $query): \PDOStatement
    {
        $statement = $this->prepareStatement($query);
        $statement->execute();

        return $statement;
    }

    protected function prepareStatement(RawQuery $query): \PDOStatement
    {
        $this->open();

        $statement = $this->getPdo()->prepare($query->getQuery(), $query->getConfig() ?? []);

        $statement = $this->runMiddleware(self::STEP_BEFORE_PREPARE, $statement, $query);

        $params = $query->getParams() ?? [];

        foreach ($params as $paramName => [$value, $type]) {
            if ($type === null) {
                if (is_int($value)) {
                    $type = \PDO::PARAM_INT;
                } else if (is_null($value)) {
                    $type = \PDO::PARAM_NULL;
                } else if (is_bool($value)) {
                    $type = \PDO::PARAM_BOOL;
                } else {
                    $type = \PDO::PARAM_STR;
                }
            }

            $statement->bindParam($paramName, $value, $type);
        }

        return $this->runMiddleware(self::STEP_AFTER_PREPARE, $statement, $query);
    }

    public function fetchFirst(RawQuery $query)
    {
        $query = $this->runMiddleware(self::STEP_BEFORE_RUN, $query, 'first');

        $statement = $this->executeStatement($query);
        $result = $statement->fetch($this->fetchMode);
        $statement->closeCursor();

        return $this->runMiddleware(self::STEP_AFTER_RUN, $result, $query, 'first');
    }

    public function fetchAll(RawQuery $query): array
    {
        $query = $this->runMiddleware(self::STEP_BEFORE_RUN, $query, 'all');

        $statement = $this->executeStatement($query);
        $result = $statement->fetchAll($this->fetchMode);
        $statement->closeCursor();

        return $this->runMiddleware(self::STEP_AFTER_RUN, $result, $query, 'all');
    }

    public function fetch(RawQuery $query): ResultBuilder
    {
        $query = $this->runMiddleware(self::STEP_BEFORE_RUN, $query, 'builder');

        $result = QueryResultBuilder::create()
            ->useReader($this->createResultReader($query));

        return $this->runMiddleware(self::STEP_AFTER_RUN, $result, $query, 'builder');
    }

    protected function createResultReader(RawQuery $query)
    {
        $result = PdoResultReader::createFromStatement($this->prepareStatement($query));
        $result->fetchMode = $this->fetchMode;

        return $result;
    }

    public function fetchReader(RawQuery $query): ResultReader
    {
        $query = $this->runMiddleware(self::STEP_BEFORE_RUN, $query, 'reader');

        $result = $this->createResultReader($query);

        return $this->runMiddleware(self::STEP_AFTER_RUN, $result, $query, 'reader');
    }

    public function appendMiddleware(string $step, callable $middleware): Driver
    {
        $this->middlewares[$step][] = $middleware;
        return $this;
    }
}