# Getting Started

This guide will get you from an empty project to running your first query.

## Installation

Installation is done via composer `composer install arekxv/pql`

## Picking a database

First you decide on a database to be used. Every database is supported through a driver
and a query builder. The driver handles the connection to the database and executes the
compiled queries, the builder is used to compile the queries into a format that the driver
can understand.

Following systems are supported:

* [MySQL](drivers/mysql.md) - MySQL database via PDO
* [PostgreSQL](drivers/pgsql.md) - PostgreSQL database via PDO
* [SQLite](drivers/sqlite.md) - SQLite database via PDO
* [SQL Server](drivers/sqlsrv.md) - Microsoft SQL Server database via PDO

## Creating a runner

The easiest way to start is to let `PdoDatabase` resolve everything for you. It figures out
the driver and the query builder from the DSN you pass in and returns a ready to use
`QueryRunner`:

```php
use ArekX\PQL\Drivers\Pdo\PdoDatabase;

$runner = PdoDatabase::resolve([
    'dsn' => 'mysql:host=127.0.0.1;dbname=your_database',
    'username' => 'username',
    'password' => 'password',
]);
```

The scheme of the DSN (the part before the first colon) decides which driver is used,
so `mysql:` resolves to MySQL, `pgsql:` to PostgreSQL, `sqlite:` to SQLite and
`sqlsrv:` (or `dblib:`) to SQL Server.

You only need to do this once in your application. The runner combines the driver and the
builder into a single package so that you can just pass queries into it.

If you want more control you can also create the driver and the builder by hand. See the
driver pages for how to do that and for driver specific options.

## Running your first query

To run a query you need to create a query object and pass it into the runner.
Lets do a simple select query to get all users from the database:

```php
use function \ArekX\PQL\Sql\{select, all};

$query = select('*')->from('users')->where(all(['is_active' => 1]));

$results = $runner->fetchAll($query); // Results are returned here.
```

That is it. You created a runner, defined a query and ran it.

## Where to go next

* [Writing Queries](writing-queries.md) - All query types and helper functions you can use to build queries.
* [Statements](statements.md) - Calling procedures, functions and using CASE statements.
* [Result Builder](result-builder.md) - Processing results after they are returned from the database.
* [Architecture](architecture.md) - How the library is put together.
