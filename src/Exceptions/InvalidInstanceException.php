<?php
/**
 * @author Aleksandar Panic
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @since 1.0.0
 **/

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