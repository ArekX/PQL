# PostgreSQL Driver

PostgreSQL driver is a driver for the PostgreSQL database. It uses PDO to connect to the database and execute queries.

# Usage

To use the PostgreSQL driver you need to create a new instance of `PgsqlDriver` and pass it into `QueryRunner`:

```php
use ArekX\PQL\Drivers\Pdo\Pgsql\PgsqlDriver;
use ArekX\PQL\Drivers\Pdo\Pgsql\PgsqlQueryBuilder;
use ArekX\PQL\QueryRunner;


$driver = PgsqlDriver::create([
    'dsn' => 'pgsql:host=127.0.0.1;port=5432;dbname=your_database',
    'username' => 'username',
    'password' => 'password',
]);

$builder = new PgsqlQueryBuilder();

// Create a runner for your driver and builder, this should be done once in your application
$runner = QueryRunner::create($driver, $builder);
```

Driver handles the PDO connection to the database and executes the compiled queries.
Builder is used to compile the queries into a format that the driver can understand.
Runner is used to execute the queries and fetch the results.

Identifiers (table and column names) are quoted using double quotes (`"name"`), as is
standard for PostgreSQL.

# Running a query

To run a query you need to create a query object and pass it into the runner.
Lets do a simple select query to get all users from the database:

```php
use function \ArekX\PQL\Sql\{select, all};

$query = select('*')->from('user')->where(all(['is_active' => 1]));

$results = $runner->fetchAll($query); // Results are returned here.
```

# Last inserted id

PostgreSQL resolves the last inserted id from a sequence. Pass the sequence name
(for a `serial`/`GENERATED` column it defaults to `<table>_<column>_seq`) to
`getLastInsertedId()`:

```php
$runner->run(insert('users', ['name' => 'New User']));

$id = $driver->getLastInsertedId('users_id_seq');
```

# Differences from MySQL

* Identifiers are quoted with double quotes (`"name"`) instead of backticks.
* PostgreSQL does not support `LIMIT`/`OFFSET` on `UPDATE` and `DELETE` statements,
  so they are not generated for those queries. Limit the affected rows with a
  sub-query in the `WHERE` condition instead.
