# Extending PQL

The library is built so that you can plug in your own pieces. This page covers the three most
common cases: registering a custom DSN scheme, writing a custom driver and writing a custom
query builder.

## Registering a custom scheme

`PdoDatabase` resolves the driver and the builder from the DSN scheme using a map on the
`$drivers` static property. You can add your own scheme to it so that `PdoDatabase::resolve()`
knows about your driver and builder:

```php
use ArekX\PQL\Drivers\Pdo\PdoDatabase;

PdoDatabase::$drivers['customscheme'] = [CustomDriver::class, CustomQueryBuilder::class];

$runner = PdoDatabase::resolve([
    'dsn' => 'customscheme:host=127.0.0.1;dbname=your_database',
]);
```

The same map can also be used to point an existing scheme to a different builder if you want to
override one.

## Writing a custom driver

A PDO driver extends `PdoDriver` and needs to implement two methods. `createPdoInstance()`
creates the actual PDO connection and `extendConfigure()` reads any extra configuration keys
that are specific to your driver:

```php
use ArekX\PQL\Drivers\Pdo\PdoDriver;
use PDO;

class CustomDriver extends PdoDriver
{
    public bool $someOption = false;

    protected function createPdoInstance(): PDO
    {
        $instance = new PDO($this->dsn, $this->username, $this->password, $this->options);

        if ($this->someOption) {
            // Apply some connection specific setup.
        }

        return $instance;
    }

    protected function extendConfigure(array $config): void
    {
        $this->someOption = $config['someOption'] ?? $this->someOption;
    }
}
```

The base driver already handles the DSN, username, password, options, transactions, middleware
and all the fetch methods, so you only need to take care of what is specific to your database.

## Writing a custom query builder

The PDO query builders share a set of builders in the `Common` namespace. A dialect specific
builder extends `CommonQueryBuilder` and configures how it differs from the SQL standard
through a few constants:

* `QUOTE_CHARACTER` - the character used to quote identifiers (defaults to `"`).
* `CLOSING_QUOTE_CHARACTER` - the closing quote character, for dialects that quote
  asymmetrically (defaults to the same as the opening one).
* `SUPPORTS_MODIFY_LIMIT` - whether `LIMIT`/`OFFSET` are allowed on `UPDATE` and `DELETE`
  (defaults to false).

The MySQL builder is a good example. It quotes with backticks and allows LIMIT/OFFSET on
UPDATE and DELETE:

```php
use ArekX\PQL\Drivers\Pdo\Common\CommonQueryBuilder;

class MySqlQueryBuilder extends CommonQueryBuilder
{
    protected const QUOTE_CHARACTER = '`';
    protected const SUPPORTS_MODIFY_LIMIT = true;
}
```

These constants are used instead of instance properties so the value is stored once per class
rather than on every instance.

### Overriding how a query is built

If a dialect needs to build a specific query type differently, you can point that query type to
your own builder through the `$builderMap`. For example, the SQL Server builder swaps in its own
select builder to translate `LIMIT`/`OFFSET` into `TOP` and `OFFSET ... FETCH`:

```php
use ArekX\PQL\Drivers\Pdo\Common\CommonQueryBuilder;
use ArekX\PQL\Sql\Query\Select;

class CustomQueryBuilder extends CommonQueryBuilder
{
    public function __construct()
    {
        $this->builderMap[Select::class] = CustomSelectBuilder::class;
    }
}
```

The map covers every query and statement type, so you can replace as little or as much of the
build process as you need while reusing everything else from the `Common` builders.
