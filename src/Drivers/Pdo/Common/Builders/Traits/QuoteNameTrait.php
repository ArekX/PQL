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

namespace ArekX\PQL\Drivers\Pdo\Common\Builders\Traits;

use ArekX\PQL\Drivers\Pdo\Common\CommonQueryBuilderState;

trait QuoteNameTrait
{
    /**
     * Quotes a name using the quote character configured on the state.
     *
     * The quote character is dialect specific (backticks for MySQL, double
     * quotes for PostgreSQL) and is read from the query builder state.
     *
     * If a name already contains the quote character this method will
     * not perform any transformations.
     *
     * @param string $name Name to be quoted.
     * @param CommonQueryBuilderState $state Query builder state holding the quote character.
     * @return string
     */
    protected function quoteName(string $name, CommonQueryBuilderState $state): string
    {
        $quote = $state->getQuoteCharacter();

        if (str_contains($name, $quote)) {
            return $name;
        }

        return preg_replace('/([a-zA-Z_]\w*)/', $quote . '$1' . $quote, $name);
    }
}
