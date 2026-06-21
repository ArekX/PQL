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

namespace ArekX\PQL\Drivers\Pdo\Common;

use ArekX\PQL\Contracts\QueryBuilder;
use ArekX\PQL\Contracts\QueryBuilderState;
use ArekX\PQL\Drivers\Pdo\Common\Builders\CallBuilder;
use ArekX\PQL\Drivers\Pdo\Common\Builders\CaseWhenBuilder;
use ArekX\PQL\Drivers\Pdo\Common\Builders\DeleteBuilder;
use ArekX\PQL\Drivers\Pdo\Common\Builders\InsertBuilder;
use ArekX\PQL\Drivers\Pdo\Common\Builders\MethodBuilder;
use ArekX\PQL\Drivers\Pdo\Common\Builders\QueryPartBuilder;
use ArekX\PQL\Drivers\Pdo\Common\Builders\RawBuilder;
use ArekX\PQL\Drivers\Pdo\Common\Builders\SelectBuilder;
use ArekX\PQL\Drivers\Pdo\Common\Builders\UnionBuilder;
use ArekX\PQL\Drivers\Pdo\Common\Builders\UpdateBuilder;
use ArekX\PQL\Sql\Query\Delete;
use ArekX\PQL\Sql\Query\Insert;
use ArekX\PQL\Sql\Query\Raw;
use ArekX\PQL\Sql\Query\Select;
use ArekX\PQL\Sql\Query\Union;
use ArekX\PQL\Sql\Query\Update;
use ArekX\PQL\Sql\SqlParamBuilder;
use ArekX\PQL\Sql\SqlQueryBuilderFactory;
use ArekX\PQL\Sql\Statement\Call;
use ArekX\PQL\Sql\Statement\CaseWhen;
use ArekX\PQL\Sql\Statement\Method;

/**
 * Shared SQL query builder factory used by the PDO drivers.
 *
 * It maps each query type to a shared builder. Dialect specific drivers
 * extend this class and configure the identifier quote character and whether
 * LIMIT/OFFSET are supported on UPDATE/DELETE statements.
 */
class CommonQueryBuilder extends SqlQueryBuilderFactory
{
    /**
     * Character used to quote identifiers (table and column names).
     *
     * Defaults to the SQL standard double quote. Dialect specific builders
     * override this constant. It is a constant rather than a property so the
     * value is stored once per class instead of on every instance.
     */
    protected const QUOTE_CHARACTER = '"';

    /**
     * Closing character used to quote identifiers, for dialects that quote
     * asymmetrically.
     *
     * Null means the closing character is the same as the opening one.
     */
    protected const CLOSING_QUOTE_CHARACTER = null;

    /**
     * Whether the dialect supports LIMIT/OFFSET on UPDATE and DELETE statements.
     *
     * Defaults to false which is the SQL standard behavior. Dialect specific
     * builders override this constant.
     */
    protected const SUPPORTS_MODIFY_LIMIT = false;

    /**
     * Builder map representing how each query will be built.
     *
     * @var string[]
     */
    public array $builderMap = [
        Raw::class => RawBuilder::class,
        Select::class => SelectBuilder::class,
        Union::class => UnionBuilder::class,
        Delete::class => DeleteBuilder::class,
        Insert::class => InsertBuilder::class,
        Update::class => UpdateBuilder::class,
        Call::class => CallBuilder::class,
        Method::class => MethodBuilder::class,
        CaseWhen::class => CaseWhenBuilder::class,
    ];

    /**
     * @inheritDoc
     */
    public function createState(): QueryBuilderState
    {
        $state = CommonQueryBuilderState::create();

        $state->setParamsBuilder(SqlParamBuilder::create());
        $state->setParentBuilder($this);
        $state->setQuoteCharacter(static::QUOTE_CHARACTER, static::CLOSING_QUOTE_CHARACTER);
        $state->setSupportsModifyLimit(static::SUPPORTS_MODIFY_LIMIT);

        return $state;
    }

    /**
     * @inheritDoc
     */
    protected function createBuilder(string $queryClass): QueryBuilder
    {
        if (empty($this->builderMap[$queryClass])) {
            throw new \UnexpectedValueException('No builder defined for: ' . $queryClass);
        }

        /** @var QueryPartBuilder $builderClass */
        $builderClass = $this->builderMap[$queryClass];

        return new $builderClass();
    }
}
