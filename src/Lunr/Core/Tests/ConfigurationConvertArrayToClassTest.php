<?php

/**
 * This file contains the ConfigurationConvertArrayToClassTest
 * class.
 *
 * @package    Lunr\Core
 * @author     Heinz Wiesinger <heinz@m2mobi.com>
 * @copyright  2011-2018, M2Mobi BV, Amsterdam, The Netherlands
 * @license    http://lunr.nl/LICENSE MIT License
 */

namespace Lunr\Core\Tests;

use Lunr\Core\Configuration;

/**
 * Test for the method convert_array_to_class().
 *
 * @covers     Lunr\Core\Configuration
 */
class ConfigurationConvertArrayToClassTest extends ConfigurationTest
{

    /**
     * TestCase Constructor.
     */
    public function setUp(): void
    {
        $this->setUpArray([]);
    }

    /**
     * Test convert_array_to_class() with non-array input values.
     *
     * @param mixed $input Various invalid values
     *
     * @dataProvider nonArrayValueProvider
     * @covers       Lunr\Core\Configuration::convert_array_to_class
     */
    public function testConvertArrayToClassWithNonArrayValues($input): void
    {
        $method = $this->get_accessible_reflection_method('convert_array_to_class');
        $this->assertEquals($input, $method->invokeArgs($this->class, [ $input ]));
    }

    /**
     * Test convert_array_to_class() with an empty array as input.
     *
     * @covers Lunr\Core\Configuration::convert_array_to_class
     */
    public function testConvertArrayToClassWithEmptyArrayValue(): void
    {
        $method = $this->get_accessible_reflection_method('convert_array_to_class');
        $output = $method->invokeArgs($this->class, [ [] ]);

        $this->assertInstanceOf('Lunr\Core\Configuration', $output);

        $property = $this->get_accessible_reflection_property('size');
        $this->assertEquals(0, $property->getValue($output));

        $property = $this->get_accessible_reflection_property('config');
        $this->assertEmpty($property->getValue($output));
    }

    /**
     * Test convert_array_to_class() with an array as input.
     *
     * @covers Lunr\Core\Configuration::convert_array_to_class
     */
    public function testConvertArrayToClassWithArrayValue(): void
    {
        $input          = [];
        $input['test']  = 'String';
        $input['test1'] = 1;

        $method = $this->get_accessible_reflection_method('convert_array_to_class');
        $output = $method->invokeArgs($this->class, [ $input ]);

        $this->assertEquals($input, $output);
    }

    /**
     * Test convert_array_to_class() with a multi-dimensional array as input.
     *
     * @depends testConvertArrayToClassWithArrayValue
     * @covers  Lunr\Core\Configuration::convert_array_to_class
     */
    public function testConvertArrayToClassWithMultidimensionalArrayValue(): void
    {
        $config                   = [];
        $config['test1']          = 'String';
        $config['test2']          = [];
        $config['test2']['test3'] = 1;
        $config['test2']['test4'] = FALSE;

        $method = $this->get_accessible_reflection_method('convert_array_to_class');
        $output = $method->invokeArgs($this->class, [ $config ]);

        $this->assertTrue(is_array($output));

        $this->assertInstanceOf('Lunr\Core\Configuration', $output['test2']);

        $property = $this->get_accessible_reflection_property('size');
        $this->assertEquals(2, $property->getValue($output['test2']));

        $property = $this->get_accessible_reflection_property('config');
        $this->assertEquals($config['test2'], $property->getValue($output['test2']));
    }

}

?>
