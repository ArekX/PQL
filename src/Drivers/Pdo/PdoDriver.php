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
use Exception;
use PDO;
use PDOStatement;

/**
 * Wrapping driver for PDO connections.
 */
abstract class PdoDriver implements Driver
{
    /**
     * Middleware step after a connection is open.
     *
     * Function signature for event:
     * ```php
     * function($result, $params, $next) {
     *    // Implementation
     *
     *    // $result is a PDO instance
     *
     *    $next($result)
     * }
     */
    const STEP_OPEN = 'open';

    /**
     * Middleware step after a connection is closed
     *
     * Function signature for event:
     * ```php
     * function($result, $params, $next) {
     *    // Implementation
     *
     *    // $result is null
     *
     *    $next($result)
     * }
     */
    const STEP_CLOSE = 'close';

    /**
     * Middleware step before a query is run.
     *
     * Function signature for event:
     * ```php
     * function($result, $params, $next) {
     *    // Implementation
     *
     *    // $result is a RawQuery
     *    // $params[0] is the type of the method used 'run', 'first', 'all', etc.
     *
     *    $next($result)
     * }
     */
    const STEP_BEFORE_RUN = 'before-run';

    /**
     * Middleware step before a statement is fully prepared.
     *
     * Function signature for event:
     * ```php
     * function($result, $params, $next) {
     *    // Implementation
     *
     *    // $result is a PdoStatement
     *    // $params[0] is RawQuery used
     *
     *    $next($result)
     * }
     */
    const STEP_BEFORE_PREPARE = 'before-prepare';

    /**
     * Middleware step after a statement is fully prepared.
     *
     * Function signature for event:
     * ```php
     * function($result, $params, $next) {
     *    // Implementation
     *
     *    // $result is a PdoStatement
     *    // $params[0] is RawQuery used
     *
     *    $next($result)
     * }
     */
    const STEP_AFTER_PREPARE = 'after-prepare';

    /**
     * Middleware step after a query is run.
     *
     * Function signature for event:
     * ```php
     * function($result, $params, $next) {
     *    // Implementation
     *
     *    // $result is a number of affected rows
     *    // $params[0] is RawQuery used
     *    // $params[1] is the type of the method used 'run', 'first', 'all', etc.
     *
     *    $next($result)
     * }
     */
    const STEP_AFTER_RUN = 'after-run';

    /**
     * Mode how to fetch the result.
     *
     * @var int
     */
    public int $fetchMode = PDO::FETCH_ASSOC;

    /**
     * Data source name (connection string) of the database
     * the user is trying to connect to.
     *
     * @var string
     */
    public string $dsn;

    /**
     * Username to be used to connect.
     *
     * @var string
     */
    public string $username;

    /**
     * Password to be used to connect.
     *
     * @var string
     */
    public string $password;

    /**
     * Additional connection options to be passed
     * to the PDO driver.
     *
     * @var array
     */
    public array $options = [];

    /**
     * List of middleware methods to be attached
     * to this connection.
     *
     * Format is:
     * ```php
     * [
     *    PdoDriver::STEP_OPEN => [
     *        function(mixed $result, array $params, callable $next) {
     *           // Implementation
     *
     *           $next($result); // Call next middleware in chain
     *        }
     *    ]
     * ]
     * ```
     *
     * @var array
     */
    protected array $middlewares = [];

    /**
     * Instance of PDO used.
     *
     * @var PDO|null
     */
    protected ?PDO $pdo = null;

    /**
     * Creates new instance of this PDO driver.
     *
     * @param array $config List of configuration to be passed to configure method.
     * @return static
     * @see PdoDriver::configure() for information about what to pass with $config
     */
    public static function create(array $config = []): static
    {
        $instance = new static();
        $instance->configure($config);

        return $instance;
    }

    /**
     * Configures the PDO driver from an array
     *
     * Array is in format:
     * ```php
     * [
     *    'fetchMode' => PDO::FETCH_ASSOC,
     *    'dsn' => 'connection string',
     *    'username' => 'user',
     *    'password' => 'pass',
     *    'options' => [
     *       // PDO options
     *    ],
     *    'middleware' => [
     *       // List of middleware events and methods to apply.
     *    ]
     * ]
     * ```
     *
     * Other configuration is passed to extendConfigure
     *
     * @param array $config
     * @return void
     * @see PdoDriver::extendConfigure()
     */
    public function configure(array $config): void
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

    /**
     * Apply list of middlewares to a specific step.
     *
     * See STEP_ constants for available steps and method signatures.
     *
     * @param string $step Step to be used.
     * @param array $middlewareList List of middleware functions to apply.
     * @return $this
     */
    public function useMiddleware(string $step, array $middlewareList): static
    {
        $this->middlewares[$step] = array_values($middlewareList);
        return $this;
    }

    /**
     * Template methods which allows further configuring
     * a class which inherits this PDO driver.
     *
     * Format of the config depends on the class inheriting this.
     *
     * @param array $config Config to be processed
     * @return void
     */
    abstract protected function extendConfigure(array $config): void;

    /**
     * Closes the connection to the PDO driver.
     *
     * @return void
     */
    public function close(): void
    {
        if ($this->pdo === null) {
            return;
        }

        $this->pdo = null;
        $this->runMiddleware(self::STEP_CLOSE);
    }

    /**
     * Runs a specific middleware methods based on a step passed.
     *
     * See STEP_ for available steps and method signatures.
     *
     * @param string $step
     * @param mixed $result
     * @param mixed ...$params
     * @return mixed
     */
    protected function runMiddleware(string $step, mixed $result = null, ...$params): mixed
    {
        if (empty($this->middlewares[$step])) {
            return $result;
        }

        return $this->executeMiddlewareChain($step, 0, $result, $params);
    }

    /**
     * Executes a method in middleware chain and allows
     * that method to control further execution via $next call
     *
     * @param string $step Step to be used
     * @param int $index Position of the middleware in the list.
     * @param mixed $result Result to pass to the middleware
     * @param array $params Params to pass into the middleware
     * @return mixed Result from that middleware
     */
    protected function executeMiddlewareChain(string $step, int $index, mixed $result, array $params): mixed
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

    /**
     * Initiate transaction handler for handling multiple
     * queries on the database in an atomic way.
     *
     * @return PdoTransaction
     */
    public function beginTransaction(): PdoTransaction
    {
        return PdoTransaction::create($this);
    }

    /**
     * Returns last inserted ID of a record
     *
     * @param string|null $sequenceName Name of the sequence to return from, if needed
     * @return false|string Last inserted ID or false if nothing was inserted
     */
    public function getLastInsertedId(string|null $sequenceName = null): false|string
    {
        return $this->getPdo()->lastInsertId($sequenceName);
    }

    /**
     * Returns an instance of a PDO used for underlying connection.
     *
     * @return PDO
     */
    public function getPdo(): PDO
    {
        $this->open();
        return $this->pdo;
    }

    /**
     * Open a connection to the database
     *
     * @return void
     */
    public function open(): void
    {
        if ($this->pdo) {
            return;
        }

        $this->pdo = $this->createPdoInstance();
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->runMiddleware(self::STEP_OPEN, $this->pdo);
    }

    /**
     * Create a new instance of the PDO for underlying connection.
     *
     * @return PDO
     */
    abstract protected function createPdoInstance(): PDO;

    /**
     * Executes a Raw Query
     *
     * @param RawQuery $query
     * @return int Returns number of affected rows
     */
    public function run(RawQuery $query): int
    {
        $query = $this->runMiddleware(self::STEP_BEFORE_RUN, $query, 'run');

        $statement = $this->executeStatement($query);
        $affected = $statement->rowCount();
        $statement->closeCursor();

        return $this->runMiddleware(self::STEP_AFTER_RUN, $affected, $query, 'run');
    }

    /**
     * Prepares and executes a PDO statement from a RawQuery
     *
     * @param RawQuery $query RawQuery to transform into a PDO statement
     * @return PDOStatement PDO statement which was executed
     */
    protected function executeStatement(RawQuery $query): PDOStatement
    {
        $statement = $this->prepareStatement($query);

        $statement->execute();

        return $statement;
    }

    /**
     * Prepares RawQuery as a PDO statement.
     *
     * @param RawQuery $query Raw query to prepare
     * @return PDOStatement Prepared PDO statement.
     */
    protected function prepareStatement(RawQuery $query): PDOStatement
    {
        $this->open();

        $statement = $this->getPdo()->prepare($query->getQuery(), $query->getConfig() ?? []);

        /** @var PDOStatement $statement */
        $statement = $this->runMiddleware(self::STEP_BEFORE_PREPARE, $statement, $query);

        $params = $query->getParams() ?? [];

        foreach ($params as $paramName => [$value, $type]) {
            if ($type === null) {
                if (is_int($value)) {
                    $type = PDO::PARAM_INT;
                } elseif (is_null($value)) {
                    $type = PDO::PARAM_NULL;
                } elseif (is_bool($value)) {
                    $type = PDO::PARAM_BOOL;
                } else {
                    $type = PDO::PARAM_STR;
                }
            }

            $statement->bindValue($paramName, $value, $type);
        }


        return $this->runMiddleware(self::STEP_AFTER_PREPARE, $statement, $query);
    }

    /**
     * Run a RawQuery and return the first record in the result.
     *
     * Type of the result depends on fetchMode.
     *
     * @param RawQuery $query Raw query to be run.
     * @return mixed Result of a first record from the query.
     * @see PdoDriver::$fetchMode
     */
    public function fetchFirst(RawQuery $query): mixed
    {

        $query = $this->runMiddleware(self::STEP_BEFORE_RUN, $query, 'first');

        $statement = $this->executeStatement($query);
        $result = $statement->fetch($this->fetchMode);
        $statement->closeCursor();

        return $this->runMiddleware(self::STEP_AFTER_RUN, $result, $query, 'first');
    }

    /**
     * Run a RawQuery and return all records in the result.
     *
     * Type of each record depends on the fetchMode
     *
     * @param RawQuery $query Raw query to be run.
     * @return array All results from the query
     * @see PdoDriver::$fetchMode
     */
    public function fetchAll(RawQuery $query): array
    {
        $query = $this->runMiddleware(self::STEP_BEFORE_RUN, $query, 'fetchAll');

        $statement = $this->executeStatement($query);
        $result = $statement->fetchAll($this->fetchMode);
        $statement->closeCursor();

        return $this->runMiddleware(self::STEP_AFTER_RUN, $result, $query, 'fetchAll');
    }

    /**
     * Run a RawQuery and return a result builder to further scope
     * down the result.
     *
     * @param RawQuery $query Raw query to process
     * @return ResultBuilder Instance of a result builder to process the result.
     */
    public function fetch(RawQuery $query): ResultBuilder
    {
        $query = $this->runMiddleware(self::STEP_BEFORE_RUN, $query, 'builder');

        $result = QueryResultBuilder::create()
            ->useReader($this->createResultReader($query));

        return $this->runMiddleware(self::STEP_AFTER_RUN, $result, $query, 'builder');
    }

    /**
     * Executes a RawQuery and returns a PDO specific reader.
     *
     * @param RawQuery $query Raw query to be processed
     * @return PdoResultReader PDO specific result reader.
     * @see PdoDriver::fetchReader()
     */
    protected function createResultReader(RawQuery $query): PdoResultReader
    {
        $result = PdoResultReader::create($this->executeStatement($query));
        $result->fetchMode = $this->fetchMode;

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function fetchReader(RawQuery $query): ResultReader
    {
        $query = $this->runMiddleware(self::STEP_BEFORE_RUN, $query, 'reader');

        $result = $this->createResultReader($query);

        return $this->runMiddleware(self::STEP_AFTER_RUN, $result, $query, 'reader');
    }

    /**
     * Appends a middleware at the end for a specific step.
     *
     * @param string $step Step to add middleware to
     * @param callable $middleware Middleware to be used.
     * @return $this
     */
    public function appendMiddleware(string $step, callable $middleware): static
    {
        $this->middlewares[$step][] = $middleware;
        return $this;
    }
}
