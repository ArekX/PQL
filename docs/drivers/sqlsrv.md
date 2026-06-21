# SQL Server Driver

SQL Server driver is a driver for the Microsoft SQL Server database. It uses PDO to connect to
the database and execute queries. It works with either the `sqlsrv` PDO driver (Microsoft) or
the `dblib` PDO driver (FreeTDS).

# Usage

To use the SQL Server driver you need to create a new instance of `SqlSrvDriver` and pass it into `QueryRunner`:

```php
use ArekX\PQL\Drivers\Pdo\SqlSrv\SqlSrvDriver;
use ArekX\PQL\Drivers\Pdo\SqlSrv\SqlSrvQueryBuilder;
use ArekX\PQL\QueryRunner;


$driver = SqlSrvDriver::create([
    // Using the Microsoft sqlsrv PDO driver:
    'dsn' => 'sqlsrv:Server=127.0.0.1,1433;Database=your_database',
    // Or using the dblib (FreeTDS) PDO driver:
    // 'dsn' => 'dblib:host=127.0.0.1:1433;dbname=your_database',
    'username' => 'username',
    'password' => 'password',
]);

$builder = new SqlSrvQueryBuilder();

// Create a runner for your driver and builder, this should be done once in your application
$runner = QueryRunner::create($driver, $builder);
```

Driver handles the PDO connection to the database and executes the compiled queries.
Builder is used to compile the queries into a format that the driver can understand.
Runner is used to execute the queries and fetch the results.

Identifiers (table and column names) are quoted using square brackets (`[name]`), as is
standard for SQL Server.

# Running a query

To run a query you need to create a query object and pass it into the runner.
Lets do a simple select query to get all users from the database:

```php
use function \ArekX\PQL\Sql\{select, all};

$query = select('*')->from('user')->where(all(['is_active' => 1]));

$results = $runner->fetchAll($query); // Results are returned here.
```

# Limiting results

SQL Server does not use the `LIMIT`/`OFFSET` syntax. The builder translates `limit()`
and `offset()` to the SQL Server equivalents:

* When only a limit is set, `SELECT TOP (n) ...` is generated:

  ```php
  select('*')->from('user')->limit(10);
  // SELECT TOP (10) * FROM [user]
  ```

* When an offset is set, `OFFSET m ROWS FETCH NEXT n ROWS ONLY` is generated. SQL Server
  requires an `ORDER BY` clause for this form, so make sure to add one:

  ```php
  select('*')->from('user')->orderBy(['id' => 'asc'])->offset(20)->limit(10);
  // SELECT * FROM [user] ORDER BY [id] ASC OFFSET 20 ROWS FETCH NEXT 10 ROWS ONLY
  ```

# Differences from MySQL

* Identifiers are quoted with square brackets (`[name]`) instead of backticks.
* `LIMIT`/`OFFSET` are replaced with `TOP` and `OFFSET ... FETCH` (see above).
* `LIMIT`/`OFFSET` are not generated for `UPDATE` and `DELETE` statements.
