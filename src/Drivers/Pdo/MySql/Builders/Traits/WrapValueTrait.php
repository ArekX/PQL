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

namespace ArekX\PQL\Drivers\Pdo\MySql\Builders\Traits;

use ArekX\PQL\Drivers\Pdo\MySql\MySqlQueryBuilderState;

trait WrapValueTrait
{
    /**
     * Wraps a value into a parameter for prepared statement
     *
     * @param mixed $value Value to be wrapped
     * @param MySqlQueryBuilderState $state Query builder state
     * @param mixed|null $type Type of the value to be passed to the driver.
     * @return mixed|string
     */
    protected function buildWrapValue(mixed $value, MySqlQueryBuilderState $state, mixed $type = null): mixed
    {
        $builder = $state->getParamsBuilder();

        if (is_array($value)) {
            $results = [];
            foreach ($value as $item) {
                $results[] = $builder->wrapValue($item, $type);
            }
            return implode(', ', $results);
        }

        return $builder->wrapValue($value, $type);
    }
}
