<?php
/**
 * Copyright 2021 Aleksandar Panic
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace ArekX\PQL\Sql;

use ArekX\PQL\Contracts\StructuredQuery;
use ArekX\PQL\Sql\Query\Delete;
use ArekX\PQL\Sql\Query\Insert;
use ArekX\PQL\Sql\Query\Raw;
use ArekX\PQL\Sql\Query\Select;
use ArekX\PQL\Sql\Query\Traits\ConditionTrait;
use ArekX\PQL\Sql\Query\Union;
use ArekX\PQL\Sql\Query\Update;
use ArekX\PQL\Sql\Statement\Call;
use ArekX\PQL\Sql\Statement\CaseWhen;
use ArekX\PQL\Sql\Statement\Method;

if (!function_exists('\ArekX\PQL\Sql\select')) {
    /**
     * Create a SELECT query.
     *
     * @param array|StructuredQuery|string|null $columns Columns to be selected
     * @return Select
     * @see Select
     */
    function select(StructuredQuery|array|string|null $columns = null): Select
    {
        return Select::create()->columns($columns);
    }
}

if (!function_exists('\ArekX\PQL\Sql\insert')) {
    /**
     * Create a INSERT query
     *
     * @param string|StructuredQuery $into Where to insert data to.
     * @param array|null $data Associative array of data to be inserted.
     * @return Insert
     */
    function insert(StructuredQuery|string $into, array $data = null): Insert
    {
        $query = Insert::create()->into($into);

        if ($data !== null) {
            $query->data($data);
        }

        return $query;
    }
}

if (!function_exists('\ArekX\PQL\Sql\delete')) {
    /**
     * Creates a DELETE query
     *
     * @param string|StructuredQuery $from From where to delete
     * @param array|StructuredQuery|null $condition Filter for deletion
     * @return Delete
     */
    function delete(StructuredQuery|string $from, StructuredQuery|array $condition = null): Delete
    {
        return Delete::create()->from($from)->where($condition);
    }
}

if (!function_exists('\ArekX\PQL\Sql\update')) {
    /**
     * Creates an UPDATE query
     *
     * @param string|StructuredQuery $to Destination which will be updated
     * @param array $data Data to be set.
     * @param array|StructuredQuery|null $condition Filter for deletion
     * @return Update
     */
    function update(StructuredQuery|string $to, array $data, StructuredQuery|array $condition = null): Update
    {
        return Update::create()->to($to)->set($data)->where($condition);
    }
}

if (!function_exists('\ArekX\PQL\Sql\union')) {
    /**
     * Creates a UNION query
     *
     * @param StructuredQuery ...$queries Queries to union
     * @return Union
     */
    function union(StructuredQuery ...$queries): Union
    {
        $unionQuery = Union::create();

        if (empty($queries)) {
            return $unionQuery;
        }

        $unionQuery->from($queries[0]);

        $max = count($queries);
        for ($i = 1; $i < $max; $i++) {
            $unionQuery->unionWith($queries[$i]);
        }

        return $unionQuery;
    }
}

if (!function_exists('\ArekX\PQL\Sql\raw')) {
    /**
     * Creates a raw query
     *
     * @param mixed $query Query to be used
     * @param array|null $params Params for the query
     * @return Raw
     */
    function raw(mixed $query, array $params = null): Raw
    {
        return Raw::from($query, $params);
    }
}

if (!function_exists('\ArekX\PQL\Sql\call')) {
    /**
     * Creates a CALL statement
     *
     * @param string $name Name of the procedure to call.
     * @param mixed ...$params Params to be passed.
     * @return Call
     */
    function call(string $name, ...$params): Call
    {
        return Call::as($name, ...$params);
    }
}

if (!function_exists('\ArekX\PQL\Sql\method')) {
    /**
     * Creates a method statement
     *
     * @param string $name Name of the procedure to call.
     * @param mixed ...$params Params to be passed.
     * @return Method
     */
    function method(string $name, ...$params): Method
    {
        return Method::as($name, ...$params);
    }
}

if (!function_exists('\ArekX\PQL\Sql\caseWhen')) {
    /**
     * Creates a CASE statement
     *
     * @param mixed|null $of Of value to be used in start of CASE
     * @param mixed ...$whenThen List of [$when, $then] statements.
     * @return CaseWhen
     */
    function caseWhen(mixed $of = null, ...$whenThen): CaseWhen
    {
        return CaseWhen::create()
            ->of($of)
            ->when($whenThen);
    }
}

if (!function_exists('\ArekX\PQL\Sql\value')) {
    /**
     * Creates a value operation
     *
     * This function is used as a helper for ['value', value].
     *
     * Values added in this way are properly parametrized
     * and escaped meaning that they this can be used
     * for user input.
     *
     * @param mixed $value Value to be wrapped
     * @return array
     * @see ConditionTrait::where()
     */
    function value(mixed $value): array
    {
        return ['value', $value];
    }
}

if (!function_exists('\ArekX\PQL\Sql\column')) {
    /**
     * Creates a column operation
     *
     * This function is used as a helper for ['column', name].
     *
     * **SQL Injection Warning**: This operation is not safe for user input.
     *
     * @param mixed $column Column to be wrapped
     * @return array
     * @see ConditionTrait::where()
     */
    function column(mixed $column): array
    {
        return ['column', $column];
    }
}

if (!function_exists('\ArekX\PQL\Sql\any')) {
    /**
     * Creates an any operation
     *
     * This function is used as a helper for ['any', associativeArray].
     *
     * **SQL Injection Warning**: Keys in the associative arrays
     * are not safe for user input, but values are properly escaped
     * and therefore are safe.
     *
     * @param array $values Associative array values
     * @return array
     * @see ConditionTrait::where()
     */
    function any(array $values): array
    {
        return ['any', $values];
    }
}

if (!function_exists('\ArekX\PQL\Sql\all')) {
    /**
     * Creates an all operation
     *
     * This function is used as a helper for ['all', associativeArray].
     *
     * **SQL Injection Warning**: Keys in the associative arrays
     * are not safe for user input, but values are properly escaped
     * and therefore are safe.
     *
     * @param array $values Associative array values
     * @return array
     * @see ConditionTrait::where()
     */
    function all(array $values): array
    {
        return ['all', $values];
    }
}

if (!function_exists('\ArekX\PQL\Sql\not')) {
    /**
     * Creates a not operation
     *
     * This function is used as a helper for ['not', expression].
     *
     * @param array $expression Expression to be negated
     * @return array
     * @see ConditionTrait::where()
     */
    function not(array $expression): array
    {
        return ['not', $expression];
    }
}

if (!function_exists('\ArekX\PQL\Sql\exists')) {
    /**
     * Creates a EXISTS operation
     *
     * This function is used as a helper for ['exists', expression].
     *
     * @param array $expression Expression to be negated
     * @return array
     * @see ConditionTrait::where()
     */
    function exists(array $expression): array
    {
        return ['exists', $expression];
    }
}

if (!function_exists('\ArekX\PQL\Sql\andWith')) {
    /**
     * Creates an and operation
     *
     * This function is used as a helper for ['and', ...expressions].
     *
     * @param array $expressions Expression to be places in AND
     * @return array
     * @see ConditionTrait::where()
     */
    function andWith(array ...$expressions): array
    {
        return ['and', ...$expressions];
    }
}

if (!function_exists('\ArekX\PQL\Sql\orWith')) {
    /**
     * Creates an or operation
     *
     * This function is used as a helper for ['or', ...expressions].
     *
     * @param array $expressions Expression to be places in AND
     * @return array
     * @see ConditionTrait::where()
     */
    function orWith(array ...$expressions): array
    {
        return ['or', ...$expressions];
    }
}

if (!function_exists('\ArekX\PQL\Sql\compare')) {
    /**
     * Creates a comparison operation
     *
     * This function is used as a helper for multiple comparison
     * operations.
     *
     * This helper allows an easier way of adding comparisons.
     *
     * @param array $a Operand A
     * @param string $b Operator: =, <>, !=, >, <, >=, <=
     * @param array $c Operand B
     * @return array
     * @see ConditionTrait::where()
     */
    function compare(array $a, string $b, array $c): array
    {
        return [$b, $a, $c];
    }
}

if (!function_exists('\ArekX\PQL\Sql\equal')) {
    /**
     * Creates an equality operation
     *
     * This function is used as a helper for multiple comparison
     * operations.
     *
     * This helper allows an easier way of adding comparisons.
     *
     * @param array $a Operand A
     * @param array $b Operand B
     * @return array
     * @see ConditionTrait::where()
     */
    function equal(array $a, array $b): array
    {
        return ['=', $a, $b];
    }
}

if (!function_exists('\ArekX\PQL\Sql\between')) {
    /**
     * Creates a BETWEEN operation
     *
     * This is a helper function for ['between', expression, fromExpression, toExpression]
     *
     * @param array $of Of value to be used in between.
     * @param array $from From value to be used in between.
     * @param array $to To value to be used in between.
     * @return array
     * @see ConditionTrait::where()
     */
    function between(array $of, array $from, array $to): array
    {
        return ['between', $of, $from, $to];
    }
}

if (!function_exists('\ArekX\PQL\Sql\search')) {
    /**
     * Creates a LIKE operation
     *
     * This is a helper function for ['like', expression, '%' . $value . '%']
     *
     * @param array $what What to search on
     * @param string $value Value to be searched. This value will be properly escaped.
     * @return array
     * @see ConditionTrait::where()
     */
    function search(array $what, string $value): array
    {
        return ['like', $what, ['value', '%' . $value . '%']];
    }
}

if (!function_exists('\ArekX\PQL\Sql\asFilters')) {
    /**
     * Processes passed values and applies them as
     * filters if they need to be added.
     *
     * Use case for this function in table grid systems
     * or places with an advanced search including multiple
     * value.
     *
     * Filters are to be passed in the following format:
     * ```php
     * [
     *   [$value1, condition],
     *   [$value2, condition],
     * ]
     * ```
     *
     * If $value1 and $value2 are not '' or null then those conditions
     * will be used. Condition is any valid condition expression used in
     * ConditionTrait::where() function.
     *
     * If value is a closure function, it will be called and if the result is
     * true then the condition on the right will be used.
     *
     * @param array $filters
     * @param string $op
     * @return array
     * @see ConditionTrait::where()
     */
    function asFilters(array $filters, string $op = 'and'): array
    {
        $results = [$op];

        foreach ($filters as [$check, $condition]) {
            if ($check === null || $check === '') {
                continue;
            }

            if ($check instanceof \Closure && !$check()) {
                continue;
            }

            $results[] = $condition;
        }

        return $results;
    }
}
