<?php

/**
 * This file contains the ConfigurationBaseTest class.
 *
 * SPDX-FileCopyrightText: Copyright 2011 M2mobi B.V., Amsterdam, The Netherlands
 * SPDX-FileCopyrightText: Copyright 2022 Move Agency Group B.V., Zwolle, The Netherlands
 * SPDX-License-Identifier: MIT
 */

namespace Lunr\Core\Tests;

/**
 * Basic tests for the Configuration class, with
 * empty initialization.
 *
 * @covers     Lunr\Core\Configuration
 */
class ConfigurationBaseTest extends ConfigurationTestCase
{

    /**
     * TestCase Constructor.
     */
    public function setUp(): void
    {
        $this->setUpArray([]);
    }

    /**
     * Test that the internal config storage is initially empty.
     */
    public function testConfigIsEmpty(): void
    {
        $this->assertPropertyEmpty('config');
    }

    /**
     * Test that the internal environment override storage is initially empty.
     */
    public function testEnvironmentOverrideIsEmpty(): void
    {
        $this->assertPropertyEmpty('environmentOverride');
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
        $this->assertPropertyEquals('sizeInvalid', FALSE);
    }

    /**
     * Test that the initial size value is zero.
     */
    public function testSizeIsZero(): void
    {
        $this->assertPropertyEquals('size', 0);
    }

    /**
     * Test that the class was initialized as a root config container.
     */
    public function testObjectIsRootConfig(): void
    {
        $this->assertPropertySame('isRootConfig', TRUE);
    }

    /**
     * Test the function __toString().
     *
     * @covers Lunr\Core\Configuration::__toString
     */
    public function testToString(): void
    {
        echo $this->class;
        $this->expectOutputString('Array');
    }

    /**
     * Test conversion to array when $config is empty.
     *
     * @depends testConfigIsEmpty
     * @covers  Lunr\Core\Configuration::toArray
     */
    public function testToArrayIsEmpty(): void
    {
        $this->assertArrayEmpty($this->class->toArray());
    }

    /**
     * Test current() returns False if $config is empty.
     *
     * @depends testConfigIsEmpty
     * @covers  Lunr\Core\Configuration::current
     */
    public function testCurrentIsFalseWithEmptyArray(): void
    {
        $this->assertFalse($this->class->current());
    }

    /**
     * Test key() returns NULL if $config is empty.
     *
     * @depends testConfigIsEmpty
     * @covers  Lunr\Core\Configuration::key
     */
    public function testKeyIsNullWithEmptyArray(): void
    {
        $this->assertNull($this->class->key());
    }

    /**
     * Test valid() returns False if $config is empty.
     *
     * @depends testConfigIsEmpty
     * @depends testCurrentIsFalseWithEmptyArray
     * @covers  Lunr\Core\Configuration::key
     */
    public function testValidIsFalseWithEmptyArray(): void
    {
        $this->assertFalse($this->class->valid());
    }

    /**
     * Test that next() increases the internal position pointer.
     *
     * @depends testPositionIsZero
     * @covers  Lunr\Core\Configuration::next
     */
    public function testNextIncreasesPosition(): void
    {
        $this->class->next();
        $this->assertPropertyEquals('position', 1);
    }

    /**
     * Test that count() returns zero.
     *
     * @depends testConfigIsEmpty
     * @covers  Lunr\Core\Configuration::count
     */
    public function testCountIsZero(): void
    {
        $this->assertEquals(0, $this->class->count());
        $this->assertEquals(0, count($this->class));
    }

}

?>
