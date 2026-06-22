# Statements

Besides the main query types, the library provides statements for things that are not full
queries on their own but are used as parts of a query or run directly. These are the `call`,
`method` and `caseWhen` statements. Just like queries they are created with helper functions
from the `ArekX\PQL\Sql` namespace and they implement the same `StructuredQuery` interface, so
they can be combined with queries in various ways.

```php
use function \ArekX\PQL\Sql\{call, method, caseWhen};
```

## Call

`call()` creates a statement to call a stored procedure or a stored function. The first
argument is the name of the procedure and the rest are the parameters to be passed:

```php
use function \ArekX\PQL\Sql\{call, value};

$query = call('update_user_status', value(5), value(1));
// CALL update_user_status(5, 1)

$runner->run($query);
```

Parameters are passed using the value helpers, so `value()` makes them properly escaped and
safe for user input. The procedure name, on the other hand, is written into the query as is, so
it must never come from the user. Treat it the same as a table or column name.

## Method

`method()` creates an SQL function or a method to be called. It works the same way as `call()`
but is meant to be used as a part of another query, such as a column or a value in a condition:

```php
use function \ArekX\PQL\Sql\{select, method, column};

$query = select([
    'id',
    'name_length' => method('LENGTH', column('name')),
])->from('users');
// SELECT `id`, LENGTH(`name`) AS `name_length` FROM `users`
```

Because a method is a structured query, it can be nested inside other methods, conditions or
queries. As with `call()`, the function name is written into the query as is and must never come
from the user. Only the params are escaped.

## Case When

`caseWhen()` creates a CASE statement. The first argument is an optional `of` value that the
CASE compares against, and the rest are the `[when, then]` pairs.

A simple CASE that compares a column against values:

```php
use function \ArekX\PQL\Sql\{caseWhen, column, value};

$status = caseWhen(column('status'),
    [value(0), value('Inactive')],
    [value(1), value('Active')]
);
```

You can also build it up with method chaining. `of()` sets the value to compare against,
`addWhen()` appends a `when`/`then` pair and `else()` sets the default result:

```php
use function \ArekX\PQL\Sql\{caseWhen, compare, column, value};

$label = caseWhen()
    ->addWhen(compare(column('age'), '>=', value(18)), value('Adult'))
    ->addWhen(compare(column('age'), '<', value(18)), value('Minor'))
    ->else(value('Unknown'));
```

When the `of` value is omitted, each `when` is treated as a full condition (built with the
condition helpers described in [Writing Queries](writing-queries.md#conditions)), which lets
you express more complex cases.

A CASE statement is most often used as a column in a select:

```php
use function \ArekX\PQL\Sql\{select, caseWhen, column, value};

$query = select([
    'id',
    'status_label' => caseWhen(column('status'),
        [value(0), value('Inactive')],
        [value(1), value('Active')]
    ),
])->from('users');
```
