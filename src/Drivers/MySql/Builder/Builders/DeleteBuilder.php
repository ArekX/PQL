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

namespace ArekX\PQL\Drivers\MySql\Builder\Builders;

use ArekX\PQL\Drivers\MySql\Builder\Builders\Traits\ConditionTrait;
use ArekX\PQL\Drivers\MySql\Builder\Builders\Traits\FromPartTrait;
use ArekX\PQL\Drivers\MySql\Builder\Builders\Traits\JoinTrait;
use ArekX\PQL\Drivers\MySql\Builder\Builders\Traits\NumberPartTrait;
use ArekX\PQL\Drivers\MySql\Builder\Builders\Traits\WhereTrait;

/**
 * Represents a query builder for building a DELETE query.
 */
class DeleteBuilder extends QueryPartBuilder
{
    use FromPartTrait;
    use ConditionTrait;
    use JoinTrait;
    use WhereTrait;
    use NumberPartTrait;

    /**
     * @inheritDoc
     */
    protected function getInitialParts(): array
    {
        return ['DELETE'];
    }

    /**
     * @inheritDoc
     */
    protected function getPartBuilders(): array
    {
        return [
            'from' => fn($part, $state) => $this->buildFrom($part, $state),
            'join' => fn($part, $state) => $this->buildJoin($part, $state),
            'where' => fn($part, $state) => $this->buildWhere($part, $state),
            'limit' => fn($part) => $this->buildLimit($part),
            'offset' => fn($part) => $this->buildOffset($part),
        ];
    }


    /**
     * @inheritDoc
     */
    protected function getRequiredParts(): array
    {
        return ['from'];
    }


}