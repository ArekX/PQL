<?php

namespace tests\Factory;

use ArekX\PQL\Exceptions\InvalidInstanceException;
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
        $this->assertInstanceOf(TestClass::class, TestClass::create());
        $this->assertInstanceOf(TestDerivedClass::class, TestDerivedClass::create());
    }

    public function testInvalidDerivation()
    {
        $this->expectException(InvalidInstanceException::class);
        Factory::override(TestClass::class, TestClassWithParams::class);

        TestClass::create('a', 'b');
    }

    public function testCreateUsingCallableCallsIt()
    {
        $called = false;
        Factory::override(TestClass::class, function() use(&$called) {
            $called = true;
            return new TestClass();
        });

        $this->assertFalse($called);
        TestClass::create();
        $this->assertTrue($called);
    }

    public function testCreateOverriddenClassWhenSet()
    {
        Factory::override(TestClass::class, TestOverrideClass::class);
        $this->assertInstanceOf(TestOverrideClass::class, TestClass::create());
    }

    public function testOverrideWillResolveRecursively()
    {
        Factory::override(TestClass::class, TestOverrideClass::class);
        Factory::override(TestOverrideClass::class, TestSecondOverrideClass::class);

        $this->assertInstanceOf(TestSecondOverrideClass::class, TestClass::create());
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
        $this->assertInstanceOf(TestSecondOverrideClass::class, TestClass::create());
        $this->assertTrue($called);
    }


    public function testCreatingClassWithParamsWillPassParams()
    {
        $instance = TestClassWithParams::create('a', 'b');

        $this->assertInstanceOf(TestClassWithParams::class, $instance);
        $this->assertEquals('a', $instance->param1);
        $this->assertEquals('b', $instance->param2);
    }


    public function testCreateDerivedClassWithParams()
    {
        Factory::override(TestClassWithParams::class, TestDerivedClassWithParams::class);
        $instance = TestClassWithParams::create('a', 'b', 'c');

        $this->assertInstanceOf(TestDerivedClassWithParams::class, $instance);
        $this->assertEquals('a', $instance->param1);
        $this->assertEquals('b', $instance->param2);
        $this->assertEquals('c', $instance->param3);
    }

    public function testClassInstanceIsCreatedWhenMatchIsUsed()
    {
        Factory::override(TestClassWithParams::class, TestDerivedClassWithParams::class);
        $instance = TestClassWithParams::create('a', 'b', 'c');

        $this->assertInstanceOf(TestDerivedClassWithParams::class, $instance);
        $this->assertEquals('a', $instance->param1);
        $this->assertEquals('b', $instance->param2);
        $this->assertEquals('c', $instance->param3);
    }
}