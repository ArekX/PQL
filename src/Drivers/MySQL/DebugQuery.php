<?php

namespace ArekX\PQL\Drivers\MySQL;

use ArekX\PQL\Contracts\RawQuery;

class DebugQuery
{
    public static function getString(RawQuery $query)
    {
        $sql = $query->getQuery();

        foreach ($query->getParams() as $param => $value) {
            if (is_string($value)) {
                $value = "'" . str_replace("'", "\\'", $value) . "'";
            }

            $sql = str_replace($param, $value, $sql);
        }

        return $sql;
    }
}