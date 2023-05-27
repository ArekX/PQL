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

use ArekX\PQL\Contracts\StructuredQuery;
use ArekX\PQL\Drivers\Pdo\MySql\MySqlQueryBuilderState;

trait NumberPartTrait
{
    /**
     * Build numbered part
     *
     * @param string $part Part name to be build
     * @param int $number Number of the part
     * @return string
     */
    protected function buildNumberPart(string $part, int $number): string
    {
        return $part . $number;
    }

    /**
     * Build LIMIT part
     *
     * @param int $number Max rows to get
     * @return string
     */
    protected function buildLimit(int $number): string
    {
        return $this->buildNumberPart('LIMIT ', $number);
    }

    /**
     * Build OFFSET part
     *
     * @param int $number Rows to skip
     * @return string
     */
    protected function buildOffset(int $number): string
    {
        return $this->buildNumberPart('OFFSET ', $number);
    }
}
