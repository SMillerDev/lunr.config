<?php

/**
 * This file contains the ConfigurationArrayAccessTest class.
 *
 * SPDX-FileCopyrightText: Copyright 2011 M2mobi B.V., Amsterdam, The Netherlands
 * SPDX-FileCopyrightText: Copyright 2022 Move Agency Group B.V., Zwolle, The Netherlands
 * SPDX-License-Identifier: MIT
 */

namespace Lunr\Core\Tests;

use Lunr\Core\Configuration;
use RuntimeException;

/**
 * This tests the ArrayAccess methods of the Configuration class.
 *
 * @depends    Lunr\Core\Tests\ConfigurationArrayConstructorTest::testConfig
 * @covers     Lunr\Core\Configuration
 */
class ConfigurationArrayAccessTest extends ConfigurationTestCase
{

    /**
     * TestCase Constructor.
     */
    public function setUp(): void
    {
        $this->setUpArray($this->constructTestArray());
    }

    /**
     * Test offsetExists() with existing values.
     *
     * @param mixed $offset Offset
     *
     * @dataProvider existingConfigPairProvider
     */
    public function testOffsetExists(mixed $offset): void
    {
        $this->assertTrue($this->class->offsetExists($offset));
    }

    /**
     * Test offsetExists() with an invalid value.
     */
    public function testOffsetExistsWithInvalidValue(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Unsupported offset!');

        $this->class->offsetExists(TRUE);
    }

    /**
     * Test offsetExists() with non existing values.
     *
     * @param mixed $offset Offset
     *
     * @dataProvider nonExistingConfigPairProvider
     */
    public function testOffsetDoesNotExist(mixed $offset): void
    {
        $this->assertFalse($this->class->offsetExists($offset));
    }

    /**
     * Test offsetExists() does not autoload for non root config.
     */
    public function testOffsetExistsDoesNotAutoloadForNonRootConfig(): void
    {
        $subconfig = new Configuration(isRootConfig: FALSE);

        $this->assertFalse($subconfig->offsetExists('autoload'));
    }

    /**
     * Test offsetExists() does not autoload for integer array key.
     */
    public function testOffsetExistsDoesNotAutoloadForIntegerKey(): void
    {
        $this->assertFalse($this->class->offsetExists(0));
    }

    /**
     * Test offsetExists() does not autoload for already existing key.
     */
    public function testOffsetExistsDoesNotAutoloadForAlreadyLoadedKey(): void
    {
        $loaded = $this->get_reflection_property('loaded');

        $this->assertNotContains('test1', $loaded->getValue($this->class));

        $this->assertTrue($this->class->offsetExists('test1'));

        $this->assertNotContains('test1', $loaded->getValue($this->class));
    }

    /**
     * Test offsetExists() does not autoload for already tried non-existing value.
     */
    public function testOffsetExistsDoesNotAutoloadForAlreadyTriedKey(): void
    {
        $loaded = $this->get_reflection_property('loaded');

        $this->assertNotContains('foo', $loaded->getValue($this->class));

        $this->assertFalse($this->class->offsetExists('foo'));

        $this->assertContains('foo', $loaded->getValue($this->class));

        $this->assertFalse($this->class->offsetExists('foo'));
    }

    /**
     * Test offsetExists() does not autoload for already loaded value.
     *
     * @runInSeparateProcess
     */
    public function testOffsetExistsAutoloads(): void
    {
        $loaded = $this->get_reflection_property('loaded');

        $this->assertNotContains('autoload', $loaded->getValue($this->class));

        $this->assertTrue($this->class->offsetExists('autoload'));

        $this->assertContains('autoload', $loaded->getValue($this->class));
    }

    /**
     * Test offsetExists() does not autoload for already loaded value.
     *
     * @runInSeparateProcess
     */
    public function testOffsetExistsAutoloadsFromEnvironment(): void
    {
        $override['missing']['two'] = 'Overridden string';

        $this->setReflectionPropertyValue('environmentOverride', $override);

        $loaded = $this->get_reflection_property('loaded');

        $this->assertNotContains('missing', $loaded->getValue($this->class));

        $this->assertTrue($this->class->offsetExists('missing'));

        $this->assertContains('missing', $loaded->getValue($this->class));
    }

    /**
     * Test offsetGet() with non existing values.
     *
     * @param mixed $offset Offset
     *
     * @dataProvider existingConfigPairProvider
     */
    public function testOffsetGetWithExistingOffset(mixed $offset): void
    {
        $this->assertEquals($this->config[$offset], $this->class->offsetGet($offset));
    }

    /**
     * Test offsetGet() with non existing values.
     *
     * @param mixed $offset Offset
     *
     * @dataProvider nonExistingConfigPairProvider
     */
    public function testOffsetGetWithNonExistingOffset(mixed $offset): void
    {
        $this->assertNull($this->class->offsetGet($offset));
    }

    /**
     * Test offsetGet() with an invalid value.
     */
    public function testOffsetGetWithInvalidOffset(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Unsupported offset!');

        $this->class->offsetGet(TRUE);
    }

    /**
     * Test offsetGet() does not autoload for non root config.
     */
    public function testOffsetGetDoesNotAutoloadForNonRootConfig(): void
    {
        $subconfig = new Configuration(isRootConfig: FALSE);

        $this->assertNull($subconfig->offsetGet('autoload'));
    }

    /**
     * Test offsetGet() does not autoload for integer array key.
     */
    public function testOffsetGetDoesNotAutoloadForIntegerKey(): void
    {
        $this->assertNull($this->class->offsetGet(0));
    }

    /**
     * Test offsetGet() does not autoload for already existing key.
     */
    public function testOffsetGetDoesNotAutoloadForAlreadyLoadedKey(): void
    {
        $loaded = $this->get_reflection_property('loaded');

        $this->assertNotContains('test1', $loaded->getValue($this->class));

        $this->assertSame('String', $this->class->offsetGet('test1'));

        $this->assertNotContains('test1', $loaded->getValue($this->class));
    }

    /**
     * Test offsetGet() does not autoload for already tried non-existing value.
     */
    public function testOffsetGetDoesNotAutoloadForAlreadyTriedKey(): void
    {
        $loaded = $this->get_reflection_property('loaded');

        $this->assertNotContains('foo', $loaded->getValue($this->class));

        $this->assertNull($this->class->offsetGet('foo'));

        $this->assertContains('foo', $loaded->getValue($this->class));

        $this->assertNull($this->class->offsetGet('foo'));
    }

    /**
     * Test offsetGet() does not autoload for already loaded value.
     *
     * @runInSeparateProcess
     */
    public function testOffsetGetAutoloads(): void
    {
        $loaded = $this->get_reflection_property('loaded');

        $this->assertNotContains('autoload', $loaded->getValue($this->class));

        $this->assertInstanceOf(Configuration::class, $this->class->offsetGet('autoload'));

        $this->assertContains('autoload', $loaded->getValue($this->class));
    }

    /**
     * Test offsetGet() does not autoload for already loaded value.
     *
     * @runInSeparateProcess
     */
    public function testOffsetGetAutoloadsFromEnvironment(): void
    {
        $override['missing']['two'] = 'Overridden string';

        $this->setReflectionPropertyValue('environmentOverride', $override);

        $loaded = $this->get_reflection_property('loaded');

        $this->assertNotContains('missing', $loaded->getValue($this->class));

        $this->assertInstanceOf(Configuration::class, $this->class->offsetGet('missing'));

        $config                   = [];
        $config['test1']          = 'String';
        $config['test2']          = [];
        $config['test2']['test3'] = 1;
        $config['test2']['test4'] = FALSE;
        $config['missing']['two'] = 'Overridden string';

        $this->assertEquals($config, $this->class->toArray());

        $this->assertContains('missing', $loaded->getValue($this->class));
    }

    /**
     * Test that offsetUnset() unsets the config value.
     */
    public function testOffsetUnsetDoesUnset(): void
    {
        $this->assertArrayHasKey('test1', $this->getReflectionPropertyValue('config'));

        $this->class->offsetUnset('test1');

        $this->assertArrayNotHasKey('test1', $this->getReflectionPropertyValue('config'));
    }

    /**
     * Test that offsetUnset sets $sizeInvalid to FALSE.
     */
    public function testOffsetUnsetInvalidatesSize(): void
    {
        $this->assertFalse($this->getReflectionPropertyValue('sizeInvalid'));

        $this->class->offsetUnset('test1');

        $this->assertTrue($this->getReflectionPropertyValue('sizeInvalid'));
    }

    /**
     * Test offsetUnset() with an invalid offset.
     */
    public function testOffsetUnsetWithInvalidOffset(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Unsupported offset!');

        $this->class->offsetUnset(TRUE);
    }

    /**
     * Test offsetSet() with a given offset.
     *
     * @depends Lunr\Core\Tests\ConfigurationConvertArrayToClassTest::testConvertArrayToClassWithMultidimensionalArrayValue
     */
    public function testOffsetSetWithGivenOffset(): void
    {
        $this->assertArrayNotHasKey('test4', $this->getReflectionPropertyValue('config'));

        $this->class->offsetSet('test4', 'Value');

        $value = $this->getReflectionPropertyValue('config');

        $this->assertArrayHasKey('test1', $value);
        $this->assertEquals('Value', $value['test4']);
    }

    /**
     * Test offsetSet() with a null offset.
     *
     * @depends Lunr\Core\Tests\ConfigurationConvertArrayToClassTest::testConvertArrayToClassWithMultidimensionalArrayValue
     */
    public function testOffsetSetWithNullOffset(): void
    {
        $this->assertArrayNotHasKey(0, $this->getReflectionPropertyValue('config'));

        $this->class->offsetSet(NULL, 'Value');

        $value = $this->getReflectionPropertyValue('config');

        $this->assertArrayHasKey(0, $value);
        $this->assertEquals('Value', $value[0]);
    }

    /**
     * Test offsetSet() with an invalid offset.
     *
     * @depends Lunr\Core\Tests\ConfigurationConvertArrayToClassTest::testConvertArrayToClassWithMultidimensionalArrayValue
     */
    public function testOffsetSetWithInvalidOffset(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Unsupported offset!');

        $this->class->offsetSet(TRUE, 'Value');
    }

    /**
     * Test that offsetSet sets $sizeInvalid to FALSE.
     */
    public function testOffsetSetInvalidatesSize(): void
    {
        $this->assertFalse($this->getReflectionPropertyValue('sizeInvalid'));

        $this->class->offsetSet('test5', 'Value');

        $this->assertTrue($this->getReflectionPropertyValue('sizeInvalid'));
    }

}

?>
