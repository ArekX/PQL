<?php
/**
 * Created by Aleksandar Panic
 * Date: 24-Dec-18
 * Time: 08:44
 * License: MIT
 */

namespace ArekX\PQL\Values;


interface ValueParametersInterface
{
    public function withParam($param, $value): ValueInterface;

    public function withParams($params): ValueInterface;

    /**
     * Returns bound params to this value.
     *
     * @return mixed
     */
    public function extractParams();
}