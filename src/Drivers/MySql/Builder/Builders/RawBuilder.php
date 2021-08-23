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

use ArekX\PQL\Drivers\MySql\Builder\MySqlQueryBuilderState;

class RawBuilder extends QueryPartBuilder
{
    protected function getInitialParts(): array
    {
        return [];
    }

    protected function getRequiredParts(): array
    {
        return ['query'];
    }

    protected function getPartBuilders(): array
    {
        return [
            'query' => fn($part) => $part,
            'params' => fn($part, MySqlQueryBuilderState $state) => $this->mergeParams($part, $state)
        ];
    }

    protected function mergeParams($part, MySqlQueryBuilderState $state)
    {
        $paramsBuilder = $state->getParamsBuilder();

        foreach ($part as $key => $value) {
            $type = null;
            $paramValue = $value;

            if (is_array($value)) {
                [$paramValue, $type] = $value;
            }

            $paramsBuilder->add($key, $paramValue, $type);
        }
    }
}