<?php
/**
 * Created by Aleksandar Panic
 * Date: 23-Dec-18
 * Time: 23:41
 * License: MIT
 */

namespace ArekX\PQL\Exceptions;

class InvalidForDefinition extends \Exception
{
    public function __construct(array $definition)
    {
        $message = 'Invalid definition: ' . print_r($definition, true);
        parent::__construct($message, 0);
    }
}