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

namespace tests\Drivers\MySql\Builder\Builders\Traits\mock;

use ArekX\PQL\Drivers\MySql\Builder\Builders\Traits\WhereTrait;
use ArekX\PQL\Drivers\MySql\Builder\MySqlQueryBuilderState;

class WherePartTester
{
    use WhereTrait;

    public function build($where, MySqlQueryBuilderState $state)
    {
        return $this->buildWhere($where, $state);
    }
}