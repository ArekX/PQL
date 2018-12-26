<?php
/**
 * Created by Aleksandar Panic
 * Date: 25-Dec-18
 * Time: 21:13
 * License: MIT
 */

namespace ArekX\PQL;


use ArekX\PQL\DataSources\ListSource;

class PQL
{
    public static function from($source)
    {
        if (is_array($source)) {
            return ListSource::from($source);
        }

        if (is_iterable($source)) {

        }
    }
}