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
    public static function wrap($value, $params = []): ValueInterface;

    public function withParam($param, $value): ValueInterface;

    public function withParams($params): ValueInterface;

    /**
     * Extracts value from interface.
     *
     * @return mixed
     */
    public function extractValue();


    /**
     * Returns bound params to this value.
     *
     * @return mixed
     */
    public function extractParams();
}