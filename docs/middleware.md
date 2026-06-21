# Middleware

Middleware lets you hook into the driver at specific steps while a query is being run. It is
the intended way to add things like logging, profiling, debugging or rewriting a query before
it is executed, without having to change the driver itself.

A middleware is a function with the following signature:

```php
function ($result, $params, $next) {
    // Do something with $result

    return $next($result);
}
```

`$result` is the value for the current step (described below), `$params` holds any extra data
for the step and `$next` passes the result to the next middleware in the chain. You should
always call `$next($result)` and return its value so the chain continues, unless you
deliberately want to stop it.

## Steps

Middleware is attached to a step. The driver runs these steps in the following order while
processing a query:

* `PdoDriver::STEP_BEFORE_RUN` - Before a query is run. `$result` is the raw query and
  `$params[0]` is the type of the call (`run`, `first`, `fetchAll`, etc.).
* `PdoDriver::STEP_OPEN` - After a connection is opened. `$result` is the PDO instance.
* `PdoDriver::STEP_BEFORE_PREPARE` - Before a statement is prepared. `$result` is the PDO
  statement and `$params[0]` is the raw query.
* `PdoDriver::STEP_AFTER_PREPARE` - After a statement is prepared. `$result` is the PDO
  statement and `$params[0]` is the raw query.
* `PdoDriver::STEP_AFTER_RUN` - After a query is run. `$result` is the result of the call and
  `$params[0]` is the raw query.
* `PdoDriver::STEP_CLOSE` - After a connection is closed. `$result` is null.

The connection steps (`STEP_OPEN`, `STEP_CLOSE`) only run when the connection is actually
opened or closed, so they will not fire on every query.

## Adding middleware

You can pass middleware when configuring the driver:

```php
use ArekX\PQL\Drivers\Pdo\PdoDriver;

$driver->configure([
    'middleware' => [
        PdoDriver::STEP_BEFORE_RUN => [
            function ($result, $params, $next) {
                // $result is the raw query about to run
                error_log('Running: ' . $result->getQuery());
                return $next($result);
            },
        ],
    ],
]);
```

You can also add a single middleware to a step at any time with `appendMiddleware()`:

```php
$driver->appendMiddleware(PdoDriver::STEP_AFTER_RUN, function ($result, $params, $next) {
    return $next($result);
});
```

`useMiddleware()` replaces the whole list of middleware for a step, while `appendMiddleware()`
adds one to the end of the existing list.

## Example: query logging

A simple way to log every query and how long it took:

```php
use ArekX\PQL\Drivers\Pdo\PdoDriver;

$queries = [];

$driver->configure([
    'middleware' => [
        PdoDriver::STEP_BEFORE_RUN => [
            function ($result, $params, $next) use (&$queries) {
                $queries[] = $result->getQuery();
                return $next($result);
            },
        ],
    ],
]);
```

Every query that runs through the driver is now collected in `$queries`.
