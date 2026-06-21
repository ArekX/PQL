# Result Builder

Results from the driver are passed into a result builder. The result builder allows for a
final preprocessing step before the data is actually returned. You can use a result builder
to set a pipeline for processing specific results and create things like an ORM.

## Fetching results

The runner offers a few different ways of retrieving data from a query. Which one you use
depends on whether you want one record, multiple records, or to process the result through a
result builder.

```php
// Run a query without returning data, returns the number of affected rows.
$runner->run($query);

// Return only the first record.
$runner->fetchFirst($query);

// Return all records.
$runner->fetchAll($query);

// Return a result builder for further processing.
$runner->fetch($query);

// Return a result reader for reading rows one by one.
$runner->fetchReader($query);
```

`run()`, `fetchFirst()` and `fetchAll()` are the quick ways to get data out. When you need to
process the result before using it, you use `fetch()` which returns a result builder.

## Reading the result

The result builder returned from `fetch()` gives you several ways of reading the data:

```php
$result = $runner->fetch($query);

$result->all();       // All rows as an array.
$result->result();    // All rows with the processing pipeline applied.
$result->first();     // Only the first row.
$result->scalar();    // The value of the first column in the first row.
$result->column();    // An array of all values from a single column.
$result->exists();    // Whether there is a result to be returned.
```

`scalar()` and `column()` accept an optional column index when you want a column other than
the first one:

```php
$count = $runner->fetch(select(raw('COUNT(*)'))->from('users'))->scalar();
```

## Processing pipeline

The interesting part of the result builder is the processing pipeline. You can add a series
of processing methods which are then applied, in the same order they were added, when you call
`result()`. This lets you reshape the data without looping over it yourself.

### Index by

`pipeIndexBy()` indexes all results by a specified column. For example, with this result:

```php
[
    ['name' => 'John', 'age' => 20],
    ['name' => 'Sally', 'age' => 25],
    ['name' => 'Sue', 'age' => 32],
]
```

indexing by `age`:

```php
$result = $runner->fetch($query)->pipeIndexBy('age')->result();
```

results in:

```php
[
    20 => ['name' => 'John', 'age' => 20],
    25 => ['name' => 'Sally', 'age' => 25],
    32 => ['name' => 'Sue', 'age' => 32],
]
```

### List by

`pipeListBy()` turns the result into an associative array where the key is taken from one
column and the value from another:

```php
$result = $runner->fetch($query)->pipeListBy('id', 'name')->result();
// [1 => 'John', 2 => 'Sally', 3 => 'Sue']
```

### Map

`pipeMap()` applies a mapper function to every row:

```php
$result = $runner->fetch($query)
    ->pipeMap(fn($row) => $row['name'])
    ->result();
```

### Filter

`pipeFilter()` removes rows for which the filter function returns false:

```php
$result = $runner->fetch($query)
    ->pipeFilter(fn($row) => $row['is_active'] === 1)
    ->result();
```

### Sort

`pipeSort()` sorts the result using a comparison function which returns -1, 0 or 1:

```php
$result = $runner->fetch($query)
    ->pipeSort(fn($a, $b) => $a['age'] <=> $b['age'])
    ->result();
```

### Reduce

`pipeReduce()` reduces the result to a single value using a reducer function and an optional
initial value:

```php
$total = $runner->fetch($query)
    ->pipeReduce(fn($carry, $row) => $carry + $row['amount'], 0)
    ->result();
```

### Combining steps

Pipeline steps can be combined and they are applied in the order they were added. You can also
clear the currently set pipeline with `clearPipeline()`:

```php
$result = $runner->fetch($query)
    ->pipeFilter(fn($row) => $row['is_active'] === 1)
    ->pipeMap(fn($row) => $row['name'])
    ->result();
```

## Result reader

If you want to read rows one by one instead of loading everything at once, `fetchReader()`
returns a result reader. The result builder uses a reader internally, but you can also use it
directly:

```php
$reader = $runner->fetchReader($query);

$reader->getNextRow();      // Next row, or false when there are none left.
$reader->getNextColumn();   // Next column value.
$reader->getAllRows();      // All remaining rows.
$reader->getColumnRows();   // All values from a single column.
$reader->reset();           // Reset the reader to read from the start again.
$reader->finalize();        // Finalize the reader once you are done.
```

This is useful when you process large result sets and do not want to keep all rows in memory.
