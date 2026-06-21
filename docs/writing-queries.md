# Writing Queries

Queries are defined in an eloquent way allowing you to write almost all kinds of queries
without having to rely on passing raw query data. You create a query by using various helper
functions like `select()`, `insert()`, `update()`, `delete()`, `union()` and `raw()` or their
class counter parts `Select`, `Insert`, `Update`, `Delete`, `Union` and `Raw`.

Each of these support method chaining for adding conditions, joins, limits, offsets, etc.
All of them inherit from the same `StructuredQuery` interface and can be combined between in
various ways opening up many different approaches for any kind of complex query.

All examples here use the helper functions which are available in the `ArekX\PQL\Sql`
namespace:

```php
use function \ArekX\PQL\Sql\{select, insert, update, delete, union, raw};
```

The SQL shown next to each example is how it compiles for MySQL. The way identifiers are
quoted depends on the driver in use.

## Select

A select query is created with `select()` and is used to retrieve data. Columns are passed
to `select()` and the data source is set with `from()`:

```php
$query = select('*')->from('users');
// SELECT * FROM `users`
```

### Columns

Columns can be passed as a string, as an array, or as a sub-query. When an array is passed
the key is used as an alias and the value is used as the column name:

```php
select(['id', 'full_name' => 'name'])->from('users');
// SELECT `id`, `name` AS `full_name` FROM `users`
```

You can add more columns to an existing query with `addColumns()`:

```php
select('id')->from('users')->addColumns(['name', 'email']);
```

### From

`from()` sets the data source. Just like with columns, when an array is passed the key is
used as an alias:

```php
select('*')->from(['u' => 'users']);
// SELECT * FROM `users` AS `u`
```

A sub-query can also be used as a source by passing another query.

### Where

`where()` sets the filter for the results. Conditions are built with the condition helpers
which are described in the [Conditions](#conditions) section below:

```php
use function \ArekX\PQL\Sql\all;

select('*')->from('users')->where(all(['is_active' => 1]));
// SELECT * FROM `users` WHERE `is_active` = 1
```

You can append more conditions to an existing where with `andWhere()` and `orWhere()`. These
combine the previous condition with the new one using AND or OR:

```php
select('*')
    ->from('users')
    ->where(all(['is_active' => 1]))
    ->andWhere(all(['is_admin' => 0]));
```

### Joins

Joins are added with `innerJoin()`, `leftJoin()`, `rightJoin()` and `fullOuterJoin()`. The
source to join with is the first argument and the join condition is the second. When the
source is passed as an associative array the key is used as the alias:

```php
select('*')
    ->from(['u' => 'users'])
    ->innerJoin(['r' => 'user_role'], 'u.role_id = r.id');
// SELECT * FROM `users` AS `u` INNER JOIN `user_role` AS `r` ON u.role_id = r.id
```

If you need a join type that is not covered by the helpers you can use `join()` directly and
pass the type as the first argument:

```php
select('*')->from('users')->join('cross', 'logs');
```

### Order by

`orderBy()` accepts an associative array where the key is the column and the value is the
direction (`asc`/`desc` or `SORT_ASC`/`SORT_DESC`):

```php
select('*')->from('users')->orderBy(['name' => 'asc', 'id' => 'desc']);
// SELECT * FROM `users` ORDER BY `name` ASC, `id` DESC
```

### Group by and having

`groupBy()` accepts an array where every element is a column to group by. `having()` accepts
the same conditions as `where()` and can be appended to with `andHaving()` and `orHaving()`:

```php
use function \ArekX\PQL\Sql\{compare, column, value};

select(['role_id', 'total' => raw('COUNT(*)')])
    ->from('users')
    ->groupBy(['role_id'])
    ->having(compare(column('total'), '>', value(5)));
```

### Limit and offset

`limit()` sets how many rows to return and `offset()` sets how many rows to skip:

```php
select('*')->from('users')->limit(10)->offset(20);
// SELECT * FROM `users` LIMIT 10 OFFSET 20
```

## Insert

An insert query is created with `insert()`. The easiest way is to pass the table and an
associative array of data:

```php
insert('users', [
    'name' => 'John',
    'username' => 'john',
    'password' => 'secret',
]);
// INSERT INTO `users` (`name`, `username`, `password`) VALUES (?, ?, ?)
```

The values are properly escaped which makes this suitable for user input.

If you need more control you can set the columns and values separately. `columns()` sets the
columns and `values()` appends a set of values, so you can call it multiple times to insert
multiple rows:

```php
insert('users')
    ->columns(['name', 'username'])
    ->values(['John', 'john'])
    ->values(['Jane', 'jane']);
```

## Update

An update query is created with `update()`. It accepts the table, the data to set and an
optional condition:

```php
update('users', ['is_active' => 0], all(['id' => 5]));
// UPDATE `users` SET `is_active` = ? WHERE `id` = 5
```

You can also build it up with method chaining. `to()` sets the destination, `set()` sets the
data and `where()` (along with `andWhere()`/`orWhere()`) sets the condition. The keys in the
data are not escaped, so they should not contain user input, but the values are escaped and
can be user input.

```php
update('users')
    ->set(['is_active' => 0])
    ->where(all(['id' => 5]));
```

## Delete

A delete query is created with `delete()`. It accepts the table and an optional condition:

```php
delete('users', all(['id' => 5]));
// DELETE FROM `users` WHERE `id` = 5
```

It can also be chained:

```php
delete('users')->where(all(['is_active' => 0]));
```

## Union

A union query is created with `union()` by passing the queries to combine:

```php
$query = union(
    select('*')->from('active_users'),
    select('*')->from('archived_users')
);
// SELECT * FROM `active_users` UNION SELECT * FROM `archived_users`
```

You can also build it up by starting from one query and appending others with `unionWith()`,
which accepts an optional union type (for example `all`):

```php
union(select('*')->from('active_users'))
    ->unionWith(select('*')->from('archived_users'), 'all');
```

## Raw

When you need full control, or you want to run something the builder does not support, you
can use a raw query. `raw()` accepts the query and an optional array of parameters. Each
parameter is an array of `[value, type]` where the type is optional and inferred from the
value when not passed:

```php
$query = raw('SELECT * FROM users WHERE id = :id', [
    ':id' => [5, null],
]);
```

A raw query can also be used anywhere a query is expected, such as a column, a source or a
condition, which makes it useful for parts of a query that are not directly supported.

## Conditions

Conditions are used by `where()`, `having()` and join conditions. A condition is an array
where the first element is the operation and the rest are its operands. Instead of writing
these arrays by hand you can use the condition helper functions from the `ArekX\PQL\Sql`
namespace.

### Values and columns

The two basic building blocks are `value()` and `column()`. `value()` wraps a value so it is
properly parametrized and escaped, which means it is safe for user input. `column()`
represents a column name or a `table.column` name and is **not** safe for user input:

```php
use function \ArekX\PQL\Sql\{value, column};

value(5);          // a parametrized value
column('users.id'); // a column reference
```

### all and any

`all()` and `any()` accept an associative array where the key is the column and the value is
a value to compare against. `all()` joins the conditions with AND and `any()` joins them with
OR. The keys are not escaped (so no user input there), but the values are properly escaped:

```php
use function \ArekX\PQL\Sql\{all, any};

all(['is_active' => 1, 'is_admin' => 0]);
// `is_active` = 1 AND `is_admin` = 0

any(['role_id' => 1, 'role_id' => 2]);
// `role_id` = 1 OR `role_id` = 2
```

### Comparisons

`equal()` builds an equality check and `compare()` builds any comparison by passing the
operator as the middle argument. Supported operators are `=`, `<>`, `!=`, `>`, `<`, `>=` and
`<=`:

```php
use function \ArekX\PQL\Sql\{equal, compare, column, value};

equal(column('id'), value(5));
// `id` = 5

compare(column('age'), '>=', value(18));
// `age` >= 18
```

### and, or and not

`andWith()` and `orWith()` combine multiple expressions, and `not()` negates one:

```php
use function \ArekX\PQL\Sql\{andWith, orWith, not, all};

andWith(all(['is_active' => 1]), all(['is_admin' => 0]));

not(all(['is_active' => 1]));
```

### between

`between()` checks whether a value is between two others:

```php
use function \ArekX\PQL\Sql\{between, column, value};

between(column('age'), value(18), value(65));
// `age` BETWEEN 18 AND 65
```

### search (LIKE)

`search()` builds a LIKE expression and wraps the value in `%...%` for you. The value is
properly escaped:

```php
use function \ArekX\PQL\Sql\{search, column};

search(column('name'), 'john');
// `name` LIKE '%john%'
```

### exists

`exists()` builds an EXISTS expression, usually used together with a sub-query:

```php
use function \ArekX\PQL\Sql\exists;

exists(select('*')->from('logs')->where(all(['user_id' => column('users.id')])));
```

### asFilters

`asFilters()` is a helper for places with advanced search where you have multiple values that
should only be applied as filters when they are present. It is useful for table grid systems.

Filters are passed as a list of `[value, condition]` pairs. If a value is `null` or an empty
string the condition is skipped, otherwise it is added. If the value is a closure it is called
and the condition is used only when it returns true:

```php
use function \ArekX\PQL\Sql\{asFilters, all, search, column};

$name = $_GET['name'] ?? null;
$roleId = $_GET['role_id'] ?? null;

$query = select('*')->from('users')->where(asFilters([
    [$name, search(column('name'), (string)$name)],
    [$roleId, all(['role_id' => $roleId])],
]));
```

Only the filters whose value is present end up in the query.

## Sub-queries

Because every query implements the same `StructuredQuery` interface, any query can be used
inside another one. A query can be a column, a source, a value in a condition or anything
else where a query is expected. This is how complex queries are composed:

```php
use function \ArekX\PQL\Sql\{select, equal, column, value};

$query = select('*')
    ->from(['u' => 'users'])
    ->innerJoin(['r' => 'user_role'], 'u.role_id = r.id')
    ->where(['all', [
        'u.is_active' => 1,
        'r.id' => select('role_id')
            ->from('application_roles')
            ->where(equal(column('application_id'), value(2))),
    ]]);
/*
SELECT *
FROM `users` AS `u`
INNER JOIN `user_role` AS `r` ON u.role_id = r.id
WHERE
  `u`.`is_active` = 1
  AND `r`.`id` IN (
    SELECT `role_id` FROM `application_roles` WHERE `application_id` = 2
  )
*/
```
