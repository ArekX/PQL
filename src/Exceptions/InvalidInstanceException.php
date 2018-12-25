<?php
/**
 * Created by Aleksandar Panic
 * Date: 25-Dec-18
 * Time: 22:33
 * License: MIT
 */

namespace ArekX\PQL\Exceptions;

class InvalidInstanceException extends \Exception
{
    public $instance;

    public function __construct($instance, $ofClass)
    {
        $this->instance = $instance;
        $actualClass = get_class($instance);
        parent::__construct("Instance must derive: {$ofClass} but got: {$actualClass}");
    }
}