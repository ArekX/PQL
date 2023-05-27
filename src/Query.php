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

namespace ArekX\PQL;

use ArekX\PQL\Contracts\StructuredQuery;

/**
 * Represents high-level structured query for
 * defining a query structure for all queries.
 */
class Query implements StructuredQuery
{
    /**
     * Defined query parts
     *
     * @var array
     */
    protected array $parts = [];

    /**
     * @inheritDoc
     */
    public static function create(): static
    {
        return new static();
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return $this->parts;
    }

    /**
     * @inheritDoc
     */
    public function use(string $part, array|string|StructuredQuery|null $value): static
    {
        $this->parts[$part] = $value;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function append(string $part, array|string|StructuredQuery $value): static
    {
        if (!empty($this->parts[$part]) && !is_array($this->parts[$part])) {
            $this->parts[$part] = [$this->parts[$part]];
        }

        $this->parts[$part][] = $value;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function get(string $part): array|string|StructuredQuery|null
    {
        return $this->parts[$part] ?? null;
    }
}
