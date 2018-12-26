<?php

namespace tests\factory;

use ArekX\PQL\Factory;

/**
 * Created by Aleksandar Panic
 * Date: 26-Dec-18
 * Time: 22:29
 * License: MIT
 */

class FactoryFromTest extends \PHPUnit\Framework\TestCase
{
    public function testCreateUsingFromCreatesThatClass()
    {
        $this->assertInstanceOf(TestClass::class, TestClass::makeFrom());
        $this->assertInstanceOf(TestDerivedClass::class, TestDerivedClass::makeFrom());
    }

    public function testCreateUsingCallableCallsIt()
    {
        $called = false;
        Factory::override(TestClass::class, function() use(&$called) {
            $called = true;
            return new TestClass();
        });

        $this->assertFalse($called);
        TestClass::makeFrom();
        $this->assertTrue($called);
    }

    public function testCreateOverriddenClassWhenSet()
    {
        Factory::override(TestClass::class, TestOverrideClass::class);
        $this->assertInstanceOf(TestOverrideClass::class, TestClass::makeFrom());
    }

    public function testOverrideWillResolveRecursively()
    {
        Factory::override(TestClass::class, TestOverrideClass::class);
        Factory::override(TestOverrideClass::class, TestSecondOverrideClass::class);

        $this->assertInstanceOf(TestSecondOverrideClass::class, TestClass::makeFrom());
    }

    public function testCallingMapWillHaveSameConfigAsOverride()
    {
        $called = false;
        Factory::map([
            TestClass::class => TestOverrideClass::class,
            TestOverrideClass::class => function() use(&$called) {
                $called = true;
                return new TestSecondOverrideClass();
            }
        ]);

        $this->assertFalse($called);
        $this->assertInstanceOf(TestSecondOverrideClass::class, TestClass::makeFrom());
        $this->assertTrue($called);
    }
}