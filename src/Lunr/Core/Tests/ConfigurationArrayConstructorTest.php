<?php

/**
 * This file contains the ConfigurationArrayConstructorTest class.
 *
 * SPDX-FileCopyrightText: Copyright 2011 M2mobi B.V., Amsterdam, The Netherlands
 * SPDX-FileCopyrightText: Copyright 2022 Move Agency Group B.V., Zwolle, The Netherlands
 * SPDX-License-Identifier: MIT
 */

namespace Lunr\Core\Tests;

/**
 * This tests the Configuration class when providing an
 * non-empty array as input to the constructor.
 *
 * @depends    Lunr\Core\Tests\ConfigurationConvertArrayToClassTest::testConvertArrayToClassWithMultidimensionalArrayValue
 * @covers     Lunr\Core\Configuration
 */
class ConfigurationArrayConstructorTest extends ConfigurationTestCase
{

    /**
     * TestCase Constructor.
     */
    public function setUp(): void
    {
        $this->setUpArray($this->constructTestArray());
    }

    /**
     * Test that the internal position pointer is initially zero.
     */
    public function testPositionIsZero(): void
    {
        $this->assertPropertyEquals('position', 0);
    }

    /**
     * Test that the initial size value is cached.
     */
    public function testSizeInvalidIsFalse(): void
    {
        $this->assertPropertySame('sizeInvalid', FALSE);
    }

    /**
     * Test that initial size of the initialized class is two.
     */
    public function testSizeIsTwo(): void
    {
        $this->assertPropertyEquals('size', 2);
    }

    /**
     * Test that the class was initialized as a root config container.
     */
    public function testObjectIsRootConfig(): void
    {
        $this->assertPropertySame('isRootConfig', TRUE);
    }

    /**
     * Test that $config is set up according to the input.
     */
    public function testConfig(): void
    {
        $output = $this->getReflectionPropertyValue('config');

        $this->assertEquals($this->config['test1'], $output['test1']);
        $this->assertInstanceOf('Lunr\Core\Configuration', $output['test2']);

        $property = $this->getReflectionProperty('isRootConfig');
        $this->assertFalse($property->getValue($output['test2']));
    }

    /**
     * Test conversion to array when $config is not empty.
     *
     * @covers Lunr\Core\Configuration::toArray
     */
    public function testToArrayEqualsInput(): void
    {
        $this->assertEquals($this->config, $this->class->toArray());
    }

    /**
     * Test Cloning the Configuration class.
     */
    public function testCloneClass(): void
    {
        $config = clone $this->class;

        $this->assertEquals($config, $this->class);
        $this->assertNotSame($config, $this->class);
    }

    /**
     * Test that current() initially points to the first element.
     *
     * @depends testConfig
     * @covers  Lunr\Core\Configuration::current
     */
    public function testCurrentIsFirstElement(): void
    {
        $this->assertEquals($this->config['test1'], $this->class->current());
    }

    /**
     * Test that key() initially points to the first element.
     *
     * @depends testConfig
     * @covers  Lunr\Core\Configuration::key
     */
    public function testKeyIsFirstElement(): void
    {
        $this->assertEquals('test1', $this->class->key());
    }

    /**
     * Test that current() does not advance the internal position pointer.
     *
     * @depends testConfig
     * @depends testPositionIsZero
     * @covers  Lunr\Core\Configuration::current
     */
    public function testCurrentDoesNotAdvancePointer(): void
    {
        $this->assertEquals($this->config['test1'], $this->class->current());
        $this->assertEquals($this->config['test1'], $this->class->current());

        $this->assertPropertyEquals('position', 0);
    }

    /**
     * Test that key() does not advance the internal position pointer.
     *
     * @depends testConfig
     * @depends testPositionIsZero
     * @covers  Lunr\Core\Configuration::key
     */
    public function testKeyDoesNotAdvancePointer(): void
    {
        $this->assertEquals('test1', $this->class->key());
        $this->assertEquals('test1', $this->class->key());

        $this->assertPropertyEquals('position', 0);
    }

    /**
     * Test that next() does advance the internal position pointer by one.
     *
     * @depends testConfig
     * @depends testPositionIsZero
     * @depends Lunr\Core\Tests\ConfigurationBaseTest::testNextIncreasesPosition
     * @covers  Lunr\Core\Configuration::next
     */
    public function testNextAdvancesPointer(): void
    {
        $this->class->next();

        $output = $this->getReflectionPropertyValue('config');

        $this->assertEquals($output['test2'], $this->class->current());
    }

    /**
     * Test that key() points to the second element after one call to next().
     *
     * @depends testConfig
     * @depends testNextAdvancesPointer
     * @covers  Lunr\Core\Configuration::key
     */
    public function testKeyIsSecondElementAfterNext(): void
    {
        $this->class->next();
        $this->assertEquals('test2', $this->class->key());
    }

    /**
     * Test that valid() returns TRUE when the element exists.
     *
     * @depends testConfig
     * @depends testNextAdvancesPointer
     * @covers  Lunr\Core\Configuration::valid
     */
    public function testValidIsTrueForExistingElement(): void
    {
        $this->assertTrue($this->class->valid());
    }

    /**
     * Test that valid() returns TRUE when the element exists, and its value is FALSE.
     *
     * @depends testConfig
     * @depends testNextAdvancesPointer
     * @covers  Lunr\Core\Configuration::valid
     */
    public function testValidIsTrueWhenElementValueIsFalse(): void
    {
        $this->class->next();
        $this->class->current()->next();

        $this->assertFalse($this->class->current()->current());
        $this->assertNotNull($this->class->current()->key());

        $this->assertTrue($this->class->current()->valid());
    }

    /**
     * Test that valid() returns FALSE when the element doesn't' exist.
     *
     * @depends testConfig
     * @depends testNextAdvancesPointer
     * @covers  Lunr\Core\Configuration::valid
     */
    public function testValidIsFalseOnNonExistingElement(): void
    {
        $this->class->next();
        $this->class->next();

        $this->assertFalse($this->class->valid());
    }

    /**
     * Test that rewind() rewinds the position counter to zero.
     *
     * @depends testNextAdvancesPointer
     * @covers  Lunr\Core\Configuration::rewind
     */
    public function testRewindRewindsPosition(): void
    {
        $this->class->next();

        $this->assertPropertyEquals('position', 1);

        $this->class->rewind();

        $this->assertPropertyEquals('position', 0);
    }

    /**
     * Test that rewind() rewinds the position counter to zero.
     *
     * @depends testNextAdvancesPointer
     * @covers  Lunr\Core\Configuration::rewind
     */
    public function testRewindRewindsPointer(): void
    {
        $this->class->next();

        $this->class->rewind();

        $this->assertEquals($this->config['test1'], $this->class->current());
    }

}

?>
