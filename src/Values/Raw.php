<?php
/**
 * Created by Aleksandar Panic
 * Date: 23-Dec-18
 * Time: 23:41
 * License: MIT
 */

namespace ArekX\PQL\Values;


class Raw implements ValueInterface, ValueParametersInterface
{
    public $value;

    public $params;

    public function __construct($value, $params = [])
    {
        $this->value = $value;
        $this->params = $params;
    }

    public static function wrap($value, $params = []): Raw
    {
        return new static($value, $params);
    }

    public function withParam($param, $value): ValueInterface
    {
        // TODO: Implement withParam() method.
    }

    public function withParams($params): ValueInterface
    {
        // TODO: Implement withParams() method.
    }

    /**
     * Extracts value from interface.
     *
     * @return mixed
     */
    public function extractValue()
    {
        // TODO: Implement extractValue() method.
    }

    /**
     * Returns bound params to this value.
     *
     * @return mixed
     */
    public function extractParams()
    {
        // TODO: Implement extractParams() method.
    }
}