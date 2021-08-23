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

namespace ArekX\PQL\Drivers\MySql\Builder\Builders\Traits;

trait QuoteNameTrait
{
    /**
     * Quotes a name variable backticks for mysql names.
     *
     * If a name already contains backtick characters this method will
     * not perform any transformations.
     *
     * @param string $name Name to be quoted.
     * @return string
     */
    protected function quoteName($name): string
    {
        if (strpos($name, '`') !== false) {
            return $name;
        }

        return preg_replace("/([a-zA-Z_][a-zA-Z0-9_]*)/", "`$1`", $name);
    }
}