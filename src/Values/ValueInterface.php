<?php
/**
 * Created by Aleksandar Panic
 * Date: 23-Dec-18
 * Time: 23:41
 * License: MIT
 */

namespace ArekX\PQL\Values;


interface ValueInterface
{
    /**
     * Extracts value from interface.
     *
     * @return mixed
     */
    public function extractValue();

}