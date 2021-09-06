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

use ArekX\PQL\Contracts\StructuredQuery;
use ArekX\PQL\Drivers\MySql\Builder\MySqlQueryBuilderState;

trait ValueColumnTrait
{
    use WrapValueTrait;
    use SubQueryTrait;
    use QuoteNameTrait;

    /**
     * Build a value/column item.
     *
     * This method accepts following:
     * ```
     * StructuredQuery
     * ['value', value]
     * ['column', columnName]
     * ```
     *
     * @param array|StructuredQuery $item Item to be built.
     * @param MySqlQueryBuilderState $state Current query builder state
     * @return mixed|string
     * @throws \Exception
     */
    protected function buildValueColumn($item, MySqlQueryBuilderState $state)
    {
        if ($item instanceof StructuredQuery) {
            return $this->buildSubQuery($item, $state);
        }

        if (!is_array($item)) {
            throw new \Exception('Param can only be an array or an instance of StructuredQuery.');
        }

        [$type, $data] = $item;

        if ($type === 'value') {
            return $this->buildWrapValue($data, $state);
        }

        if ($type === 'column') {
            return $this->quoteName($data);
        }

        throw new \Exception('Invalid type: ' . $type);
    }
}