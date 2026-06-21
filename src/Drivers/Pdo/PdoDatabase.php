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

namespace ArekX\PQL\Drivers\Pdo;

use ArekX\PQL\Contracts\QueryBuilder;
use ArekX\PQL\Drivers\Pdo\MySql\MySqlDriver;
use ArekX\PQL\Drivers\Pdo\MySql\MySqlQueryBuilder;
use ArekX\PQL\Drivers\Pdo\Pgsql\PgsqlDriver;
use ArekX\PQL\Drivers\Pdo\Pgsql\PgsqlQueryBuilder;
use ArekX\PQL\Drivers\Pdo\Sqlite\SqliteDriver;
use ArekX\PQL\Drivers\Pdo\Sqlite\SqliteQueryBuilder;
use ArekX\PQL\Drivers\Pdo\SqlSrv\SqlSrvDriver;
use ArekX\PQL\Drivers\Pdo\SqlSrv\SqlSrvQueryBuilder;
use ArekX\PQL\QueryRunner;
use InvalidArgumentException;
use UnexpectedValueException;

/**
 * Convenience factory which resolves the correct PDO driver and query builder
 * from a DSN and wires them into a ready to use query runner.
 *
 * The driver and builder are chosen based on the DSN scheme (the part before
 * the first colon), e.g. `mysql:host=...` resolves to the MySQL driver/builder.
 */
class PdoDatabase
{
    /**
     * Map of DSN scheme => [driver class, query builder class].
     *
     * Register additional drivers by adding to this map, for example:
     * ```php
     * PdoDatabase::$drivers['customscheme'] = [CustomDriver::class, CustomQueryBuilder::class];
     * ```
     *
     * @var array<string, array{0: class-string<PdoDriver>, 1: class-string<QueryBuilder>}>
     */
    public static array $drivers = [
        'mysql' => [MySqlDriver::class, MySqlQueryBuilder::class],
        'pgsql' => [PgsqlDriver::class, PgsqlQueryBuilder::class],
        'sqlite' => [SqliteDriver::class, SqliteQueryBuilder::class],
        'sqlsrv' => [SqlSrvDriver::class, SqlSrvQueryBuilder::class],
        'dblib' => [SqlSrvDriver::class, SqlSrvQueryBuilder::class],
    ];

    /**
     * Resolve a fully configured query runner from a PDO configuration.
     *
     * The driver and builder are selected automatically from the DSN scheme.
     * The resolved driver is available via the runner's `driver` property and
     * the builder via its `builder` property.
     *
     * @param array $config Configuration passed to the driver. Must contain a `dsn` key.
     * @return QueryRunner
     * @see PdoDriver::configure() for the available configuration keys.
     */
    public static function resolve(array $config): QueryRunner
    {
        return QueryRunner::create(
            static::createDriver($config),
            static::createBuilder($config['dsn'] ?? null)
        );
    }

    /**
     * Create only the PDO driver, resolved from the DSN scheme.
     *
     * @param array $config Configuration passed to the driver. Must contain a `dsn` key.
     * @return PdoDriver
     */
    public static function createDriver(array $config): PdoDriver
    {
        [$driverClass] = static::resolveClasses($config['dsn'] ?? null);

        return $driverClass::create($config);
    }

    /**
     * Create only the query builder, resolved from the DSN scheme.
     *
     * @param string|null $dsn Data source name.
     * @return QueryBuilder
     */
    public static function createBuilder(?string $dsn): QueryBuilder
    {
        [, $builderClass] = static::resolveClasses($dsn);

        return new $builderClass();
    }

    /**
     * Resolve the [driver class, builder class] pair for a DSN scheme.
     *
     * @param string|null $dsn Data source name.
     * @return array{0: class-string<PdoDriver>, 1: class-string<QueryBuilder>}
     */
    protected static function resolveClasses(?string $dsn): array
    {
        if (empty($dsn)) {
            throw new InvalidArgumentException('A "dsn" must be provided to resolve a PDO driver.');
        }

        $scheme = strtolower(explode(':', $dsn, 2)[0]);

        if (empty(static::$drivers[$scheme])) {
            throw new UnexpectedValueException(sprintf(
                'Unsupported PDO DSN scheme "%s". Supported schemes: %s',
                $scheme,
                implode(', ', array_keys(static::$drivers))
            ));
        }

        return static::$drivers[$scheme];
    }
}
