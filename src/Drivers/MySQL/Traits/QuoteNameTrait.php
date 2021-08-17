<?php

namespace ArekX\PQL\Drivers\MySQL\Traits;

trait QuoteNameTrait
{
    protected function quoteName($name): string
    {
        if (strpos($name, "'") !== false) {
            return $name;
        }

        return preg_replace("/([a-z_][a-zA-Z0-9_]*)/", "`$1`", $name);
    }
}