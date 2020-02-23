<?php
/**
 * @author Aleksandar Panic
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @since 1.0.0
 **/

namespace ArekX\PQL\Drivers\ArrayData;

use ArekX\PQL\Contracts\DriverInterface;
use ArekX\PQL\FactoryTrait;
use ArekX\PQL\Query;

class Driver implements DriverInterface
{
    use FactoryTrait;

    public function query(Query $query)
    {
        $raw = $query->raw();

        $source = $raw['source'];


    }
}