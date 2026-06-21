# Transactions

Transactions let you run multiple queries on the database in an atomic way, so that either all
of them are applied or none of them are. Transactions are handled by the driver, so you start
one from the driver and run your queries through the runner that uses that same driver.

When you create a runner with `PdoDatabase::resolve()` the driver is available on the runner's
`driver` property:

```php
$driver = $runner->driver;
```

## Running a transaction

The easiest way to use a transaction is with `execute()`. You pass it a function with your
queries and the transaction is committed when the function returns. If the function throws an
exception the transaction is rolled back and the exception is re-thrown:

```php
use function \ArekX\PQL\Sql\insert;

$runner->driver->beginTransaction()->execute(function () use ($runner) {
    $runner->run(insert('users', [
        'name' => 'John',
        'username' => 'john',
        'password' => 'secret',
    ]));

    $runner->run(insert('logs', [
        'message' => 'User created',
    ]));
});
```

If either insert fails, neither of them is applied.

## Committing and rolling back manually

If you need more control you can keep the transaction around and commit or roll back yourself:

```php
$transaction = $runner->driver->beginTransaction();

$runner->run(insert('users', ['name' => 'John']));

if ($somethingWentWrong) {
    $transaction->rollback();
} else {
    $transaction->commit();
}
```

A transaction is finalized once it is committed or rolled back. After that, any further calls
to `commit()` or `rollback()` on the same transaction are ignored, so it is safe to call them
without checking the state first.
