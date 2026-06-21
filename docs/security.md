# Security and User Input

This page explains which parts of a query are safe for user input and which are not. Getting
this right is the difference between a safe query and an SQL injection, so it is worth a read.

The rule of thumb is simple: **values are escaped, identifiers are not.** Values are passed to
the database as bound parameters, so they are safe for user input. Identifiers like table and
column names are written into the query as is, so they should never come from user input.

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

## Not safe for user input

These are written into the query without escaping, so they must never contain user input:

* Table and source names (`from()`, `into()`, `to()`).
* Column names (`columns()`, `column()`, and the **keys** in `all()`/`any()` and in `update()`'s
  data).
* Raw join conditions passed as strings.
* The query string of a `raw()` query.

If you need to use a value coming from the user as a column or table name (for example a sort
column chosen in a UI), do not pass it through directly. Validate it against a list of allowed
values first:

```php
$allowed = ['name', 'created_at', 'id'];
$sortColumn = in_array($_GET['sort'] ?? '', $allowed, true) ? $_GET['sort'] : 'id';

select('*')->from('users')->orderBy([$sortColumn => 'asc']);
```

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

When in doubt, treat anything that selects *what* to query (tables, columns) as unsafe, and
anything that is *data* as safe once it goes through a value or a parameter.
