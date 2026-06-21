# SQLite Driver

SQLite driver is a driver for the SQLite database. It uses PDO to connect to the database and execute queries.

# Usage

To use the SQLite driver you need to create a new instance of `SqliteDriver` and pass it into `QueryRunner`:

```php
use ArekX\PQL\Drivers\Pdo\Sqlite\SqliteDriver;
use ArekX\PQL\Drivers\Pdo\Sqlite\SqliteQueryBuilder;
use ArekX\PQL\QueryRunner;


$driver = SqliteDriver::create([
    'dsn' => 'sqlite:/path/to/your_database.db', // or 'sqlite::memory:' for an in-memory database
]);

$builder = new SqliteQueryBuilder();

// Create a runner for your driver and builder, this should be done once in your application
$runner = QueryRunner::create($driver, $builder);
```

SQLite has no authentication, so no username or password is required.

Driver handles the PDO connection to the database and executes the compiled queries.
Builder is used to compile the queries into a format that the driver can understand.
Runner is used to execute the queries and fetch the results.

Identifiers (table and column names) are quoted using double quotes (`"name"`), as is
standard for SQLite.

# Foreign keys

SQLite has foreign key enforcement turned off by default. To enable it for the
connection set the `foreignKeys` option:

```php
$driver = SqliteDriver::create([
    'dsn' => 'sqlite:/path/to/your_database.db',
    'foreignKeys' => true,
]);
```

# Running a query

To run a query you need to create a query object and pass it into the runner.
Lets do a simple select query to get all users from the database:

```php
use function \ArekX\PQL\Sql\{select, all};

$query = select('*')->from('user')->where(all(['is_active' => 1]));

$results = $runner->fetchAll($query); // Results are returned here.
```

# Differences from MySQL

* Identifiers are quoted with double quotes (`"name"`) instead of backticks.
* Standard SQLite builds do not support `LIMIT`/`OFFSET` on `UPDATE` and `DELETE`
  statements (that requires the `SQLITE_ENABLE_UPDATE_DELETE_LIMIT` compile option),
  so they are not generated for those queries. Limit the affected rows with a
  sub-query in the `WHERE` condition instead.
* There are no stored procedures, so the `Call` query is not applicable.
