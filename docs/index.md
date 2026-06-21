# PQL

PQL - PHP Query Language, Database Query Library

This library is a database abstraction layer which abstracts the query commands (Select, Delete, Update, etc.) out from the
drivers (MySQL, Postgres, SQL Server, etc.). Queries are defined in an eloquent way allowing you to
write almost all kinds of queries without having to rely on passing raw query data.

## Installation

Installation is done via composer `composer install arekxv/pql`

## Usage

The easiest way to start is to let `PdoDatabase` resolve the driver and the builder for you from
the DSN and give you a ready to use runner:

```php
use ArekX\PQL\Drivers\Pdo\PdoDatabase;

$runner = PdoDatabase::resolve([
    'dsn' => 'mysql:host=127.0.0.1;dbname=your_database',
    'username' => 'username',
    'password' => 'password',
]);
```

See [Getting Started](getting-started.md) for a full walkthrough.

## Guides

* [Getting Started](getting-started.md) - From installation to your first query.
* [Writing Queries](writing-queries.md) - All query types and helper functions for building queries.
* [Statements](statements.md) - Calling procedures, functions and using CASE statements.
* [Result Builder](result-builder.md) - Processing results after they are returned from the database.
* [Transactions](transactions.md) - Running multiple queries in an atomic way.
* [Middleware](middleware.md) - Hooking into the driver for logging, profiling and more.
* [Security and User Input](security.md) - What is safe for user input and what is not.
* [Extending PQL](extending.md) - Custom DSN schemes, drivers and query builders.
* [Architecture](architecture.md) - How the library is put together.

## Drivers

Following systems are supported:

* [MySQL](drivers/mysql.md) - MySQL database via PDO
* [PostgreSQL](drivers/pgsql.md) - PostgreSQL database via PDO
* [SQLite](drivers/sqlite.md) - SQLite database via PDO
* [SQL Server](drivers/sqlsrv.md) - Microsoft SQL Server database via PDO

## Testing

After installing the dependencies run `composer test`

For coverage report run `composer coverage` or you can take a look at it [here](https://scrutinizer-ci.com/g/ArekX/PQL/?branch=master).
