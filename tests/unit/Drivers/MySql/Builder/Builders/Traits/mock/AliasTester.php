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

use ArekX\PQL\Drivers\MySql\Builder\Builders\Traits\AliasTrait;
use ArekX\PQL\Drivers\MySql\Builder\MySqlQueryBuilderState;

class AliasTester
{
    use AliasTrait;

    public function alias($name, $state = null)
    {
        return $this->buildAliasedNames($name, $state ?: MySqlQueryBuilderState::create());
    }
}