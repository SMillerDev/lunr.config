<?php

/**
 * This file contains the ConfigurationArrayUsageTest class.
 *
 * @package    Lunr\Core
 * @author     Heinz Wiesinger <heinz@m2mobi.com>
 * @copyright  2011-2018, M2Mobi BV, Amsterdam, The Netherlands
 * @license    http://lunr.nl/LICENSE MIT License
 */

namespace Lunr\Core\Tests;

use Lunr\Core\Configuration;

/**
 * This tests the ArrayAccess methods of the Configuration class.
 *
 * @covers     Lunr\Core\Configuration
 */
class ConfigurationArrayUsageTest extends ConfigurationTest
{

    /**
     * TestCase Constructor.
     */
    public function setUp(): void
    {
        $this->setUpArray($this->construct_test_array());
    }

    /**
     * Test accessing the Configuration class as an array for read operations.
     *
     * @depends Lunr\Core\Tests\ConfigurationArrayAccessTest::testOffsetGetWithExistingOffset
     */
    public function testArrayReadAccess(): void
    {
        $this->assertEquals($this->config['test1'], $this->class['test1']);
    }

    /**
     * Test accessing the Configuration class as an array for write operations.
     *
     * @depends Lunr\Core\Tests\ConfigurationArrayAccessTest::testOffsetSetWithGivenOffset
     */
    public function testArrayWriteAccess(): void
    {
        $this->assertArrayNotHasKey('test4', $this->get_reflection_property_value('config'));

        $this->class['test4'] = 'Value';

        $array = $this->get_reflection_property_value('config');

        $this->assertArrayHasKey('test4', $array);
        $this->assertEquals('Value', $array['test4']);
    }

    /**
     * Test correct isset behavior on existing offsets.
     *
     * @param mixed $offset Offset
     *
     * @dataProvider existingConfigPairProvider
     * @depends      Lunr\Core\Tests\ConfigurationArrayAccessTest::testOffsetExists
     */
    public function testIssetOnExistingOffset($offset): void
    {
        $this->assertTrue(isset($this->class[$offset]));
    }

    /**
     * Test correct isset behavior on existing offsets.
     *
     * @param mixed $offset Offset
     *
     * @dataProvider nonExistingConfigPairProvider
     * @depends      Lunr\Core\Tests\ConfigurationArrayAccessTest::testOffsetDoesNotExist
     */
    public function testIssetOnNonExistingOffset($offset): void
    {
        $this->assertFalse(isset($this->class[$offset]));
    }

    /**
     * Test correct foreach behavior.
     *
     * @depends Lunr\Core\Tests\ConfigurationArrayAccessTest::testOffsetExists
     * @depends Lunr\Core\Tests\ConfigurationArrayAccessTest::testOffsetDoesNotExist
     * @depends Lunr\Core\Tests\ConfigurationArrayAccessTest::testOffsetGetWithExistingOffset
     * @depends Lunr\Core\Tests\ConfigurationArrayConstructorTest::testRewindRewindsPointer
     * @depends Lunr\Core\Tests\ConfigurationArrayConstructorTest::testValidIsTrueForExistingElement
     * @depends Lunr\Core\Tests\ConfigurationArrayConstructorTest::testValidIsTrueWhenElementValueIsFalse
     * @depends Lunr\Core\Tests\ConfigurationArrayConstructorTest::testValidIsFalseOnNonExistingElement
     * @depends Lunr\Core\Tests\ConfigurationArrayConstructorTest::testNextAdvancesPointer
     * @depends Lunr\Core\Tests\ConfigurationArrayConstructorTest::testCurrentIsFirstElement
     * @depends Lunr\Core\Tests\ConfigurationArrayConstructorTest::testKeyIsFirstElement
     */
    public function testForeach(): void
    {
        $iteration = 0;

        $config = $this->get_reflection_property_value('config');

        foreach ($this->class as $key => $value)
        {
            $this->assertEquals($config[$key], $value);
            ++$iteration;
        }

        $this->assertEquals($iteration, count($this->class));
    }

}

?>
