# Security and User Input

This page explains which parts of a query are safe for user input and which are not. Getting
this right is the difference between a safe query and an SQL injection, so it is worth a read.

The rule of thumb is simple: values are passed to the database as bound parameters, so they are
safe for user input. Table and column names are quoted before they go into the query, so a user
can no longer use them to inject SQL.

Quoting keeps the query safe, but it does not pick which table or column a name points to. A user
could still ask for a column they should not see, such as sorting by `password`. So when a name
comes from the user, you should still check it against a list of names you allow (shown below).

A few parts of a query are written in as is, with no quoting. These must never come from the
user: the query string of a `raw()` query, join conditions you pass as a plain string, and the
function name in `method()` and `call()`.

## Safe for user input

These all end up as bound parameters and are properly escaped:

* `value()` - wraps a value so it is parametrized and escaped.
* The **values** in `all()` and `any()` associative arrays.
* The data passed to `insert()` and the values passed to `update()`'s `set()`.
* The value in `search()` and the bounds in `between()`.
* The parameters passed to a `raw()` query (see below).

```php
use function \ArekX\PQL\Sql\{select, all, search, column};

// $name comes from the user, this is fine because it is a value.
$name = $_GET['name'] ?? '';

select('*')->from('users')->where(search(column('name'), $name));
```

## Quoted, but still check the name

These are quoted before they go into the query, so a user-supplied name cannot inject SQL. But it
can still point at any table or column, so check it against a list of allowed names when it comes
from the user:

* Table and source names (`from()`, `into()`, `to()`).
* Column names (`columns()`, `column()`, and the **keys** in `all()`/`any()` and in `update()`'s
  data).
* The column and the direction in `orderBy()`. The direction can only be `asc` or `desc` (or
  `SORT_ASC`/`SORT_DESC`); anything else throws an error.

If you need to use a value coming from the user as a column or table name (for example a sort
column chosen in a UI), check it against a list of allowed values first:

```php
$allowed = ['name', 'created_at', 'id'];
$sortColumn = in_array($_GET['sort'] ?? '', $allowed, true) ? $_GET['sort'] : 'id';

select('*')->from('users')->orderBy([$sortColumn => 'asc']);
```

## Never safe for user input

These go into the query exactly as you write them, with no quoting, so they must never contain
user input:

* The query string of a `raw()` query (the **params** are safe, see below).
* Join conditions you pass as a plain string.
* The function name in `method()` and `call()`.

## Raw queries

With a raw query the query string itself is not escaped, so you should never build it by
concatenating user input. Instead define parameters in the query and pass the user values
through the params, where they are bound safely:

```php
use function \ArekX\PQL\Sql\raw;

// Good: the value is bound as a parameter.
raw('SELECT * FROM users WHERE name = :name', [
    ':name' => [$userName, null],
]);

// Bad: never do this.
raw('SELECT * FROM users WHERE name = ' . $userName);
```

When in doubt: data is safe once it goes through a value or a parameter. Table and column names
are quoted, so they cannot inject SQL, but still check them against a list of allowed names when
they come from the user. And the parts in the "never safe" list above must not contain user input
at all.
