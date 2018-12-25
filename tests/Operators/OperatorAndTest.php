<?php

use ArekX\PQL\DataSources\ListSource;
use ArekX\PQL\Factory;
use ArekX\PQL\Operators\BinaryOperator;
use ArekX\PQL\PQL;
use ArekX\PQL\Values\Raw;

/**
 * Created by Aleksandar Panic
 * Date: 23-Dec-18
 * Time: 23:41
 * License: MIT
 */

class OperatorAndTest extends \PHPUnit\Framework\TestCase
{
    public function testOperatorCreated()
    {
        $filter = $this->getFilter();
        $filter
            ->define([
                ['begin', 'ast'],
                ['and', ['name', 'not', 'gte', Raw::wrap('value')]],
                ['end', 'ast'],
                'and',
                ['begin', 'ast2'],
                ['and', ['name', 'not', 'gte', Raw::wrap('value')]],
                ['end', 'ast2'],
                ['or', ['item', null]]
            ]);

        $list = [

        ];

        Factory::override(ListSource::class, function($list) {
            return new \ArekX\PQL\DataSources\TranslateableSource();
        });

        $query = ListSource::from($list)
            ->select(["name", "a.b"])
            ->filter()
                ->and(["test_id", 2])->glue()
                ->or("retries")->lt(4)
                ->and("name")->find("Test", BinaryOperator::SEARCH_USER)
                ->and("name")->eq(ListSource::from([])->select("name"))
            ->then()
                ->order()->ascBy("name")
            ->then()
                ->map()->toList('name', 'a.b')
            ->then()
                ->reduce()->toSum()
            ->then()
                ->limit()->take(10)
            ->then()
                ->fromSource();

        print_r($query->query()->filter()->extractDefinitions()); die;
    }


    protected function getFilter(): \ArekX\PQL\Filter
    {
        $filter = \ArekX\PQL\Filter::from();
        return $filter;
    }
}