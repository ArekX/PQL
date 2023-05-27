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

namespace ArekX\PQL\Drivers\Pdo\MySql;

use ArekX\PQL\Contracts\QueryBuilder;
use ArekX\PQL\Contracts\QueryBuilderState;
use ArekX\PQL\Drivers\Pdo\MySql\Builders\CallBuilder;
use ArekX\PQL\Drivers\Pdo\MySql\Builders\CaseWhenBuilder;
use ArekX\PQL\Drivers\Pdo\MySql\Builders\DeleteBuilder;
use ArekX\PQL\Drivers\Pdo\MySql\Builders\InsertBuilder;
use ArekX\PQL\Drivers\Pdo\MySql\Builders\MethodBuilder;
use ArekX\PQL\Drivers\Pdo\MySql\Builders\QueryPartBuilder;
use ArekX\PQL\Drivers\Pdo\MySql\Builders\RawBuilder;
use ArekX\PQL\Drivers\Pdo\MySql\Builders\SelectBuilder;
use ArekX\PQL\Drivers\Pdo\MySql\Builders\UnionBuilder;
use ArekX\PQL\Drivers\Pdo\MySql\Builders\UpdateBuilder;
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
 * Represents a query builder for MySQL
 */
class MySqlQueryBuilder extends SqlQueryBuilderFactory
{
    /**
     * Builder map representing how each query will be built.
     *
     * @var string[]
     */
    public $builderMap = [
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
        $state = MySqlQueryBuilderState::create();

        $state->setParamsBuilder(SqlParamBuilder::create());
        $state->setParentBuilder($this);

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
