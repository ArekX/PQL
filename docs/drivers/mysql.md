# MySQL Driver

MySQL driver is a driver for MySQL database. It uses PDO to connect to the database and execute queries.

# Usage

To use MySQL driver you need to create a new instance of `MySQLDriver` and pass it into `QueryRunner`:

```php
use ArekX\PQL\Drivers\Pdo\MySql\MySqlDriver;
use ArekX\PQL\Drivers\Pdo\MySql\MySqlQueryBuilder;
use ArekX\PQL\QueryRunner;


$driver = MySqlDriver::create([
    'dsn' => 'mysql:host=127.0.0.1;dbname=your_database',
    'username' => 'username',
    'password' => 'password',
]);

$builder = new MySqlQueryBuilder();

// Create a runner for your driver and builder, this should be done once in your application
$runner = QueryRunner::create($driver, $builder);
```

Driver handles the PDO connection to the database and executes the compiled queries.
Builder is used to compile the queries into a format that the driver can understand.
Runner is used to execute the queries and fetch the results.

# Running a query

To run a query you need to create a query object and pass it into the runner.
Lets do a simple select query to get all users from the database:

```php
use function \ArekX\PQL\Sql\{select, all};

$query = select('*')->from('user')->where(all(['is_active' => 1]));

$results = $runner->fetchAll($query); // Results are returned here.
```