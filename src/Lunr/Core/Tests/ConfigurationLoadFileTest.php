<?php

/**
 * This file contains the ConfigurationLoadFileTest class.
 *
 * SPDX-FileCopyrightText: Copyright 2011 M2mobi B.V., Amsterdam, The Netherlands
 * SPDX-FileCopyrightText: Copyright 2022 Move Agency Group B.V., Zwolle, The Netherlands
 * SPDX-License-Identifier: MIT
 */

namespace Lunr\Core\Tests;

/**
 * This tests loading configuration files via the Configuration class.
 *
 * @depends Lunr\Core\Tests\ConfigurationConvertArrayToClassTest::testConvertArrayToClassWithMultidimensionalArrayValue
 * @covers  Lunr\Core\Configuration
 */
class ConfigurationLoadFileTest extends ConfigurationTestCase
{

    /**
     * TestCase Constructor.
     */
    public function setUp(): void
    {
        $this->setUpArray($this->constructTestArray());
    }

    /**
     * Test loading a correct config file.
     *
     * @runInSeparateProcess
     *
     * @depends Lunr\Core\Tests\ConfigurationArrayConstructorTest::testToArrayEqualsInput
     * @covers  Lunr\Core\Configuration::loadFile
     */
    public function testLoadCorrectFile(): void
    {
        $this->class->loadFile('correct');

        $this->config['load']        = [];
        $this->config['load']['one'] = 'Value';
        $this->config['load']['two'] = 'String';

        $this->assertEquals($this->config, $this->class->toArray());
    }

    /**
     * Test loading a correct config file.
     *
     * @runInSeparateProcess
     *
     * @depends Lunr\Core\Tests\ConfigurationArrayConstructorTest::testToArrayEqualsInput
     * @covers  Lunr\Core\Configuration::loadFile
     */
    public function testLoadFileOverwritesValues(): void
    {
        $this->class->loadFile('overwrite');

        $config                   = [];
        $config['test1']          = 'Value';
        $config['test2']          = [];
        $config['test2']['test3'] = 1;
        $config['test2']['test4'] = FALSE;

        $this->assertEquals($config, $this->class->toArray());
    }

    /**
     * Test loading a correct config file.
     *
     * @runInSeparateProcess
     *
     * @depends Lunr\Core\Tests\ConfigurationArrayConstructorTest::testToArrayEqualsInput
     * @covers  Lunr\Core\Configuration::loadFile
     */
    public function testLoadFileMergesArrays(): void
    {
        $this->class->loadFile('merge');

        $config                   = [];
        $config['test1']          = 'String';
        $config['test2']          = [];
        $config['test2']['test3'] = 1;
        $config['test2']['test4'] = FALSE;
        $config['test2']['test5'] = 'Value';

        $this->assertEquals($config, $this->class->toArray());
    }

    /**
     * Test loading an invalid config file.
     *
     * @covers Lunr\Core\Configuration::loadFile
     */
    public function testLoadInvalidFile(): void
    {
        $before = $this->getReflectionPropertyValue('config');

        $this->class->loadFile('not_array');

        $after = $this->getReflectionPropertyValue('config');

        $this->assertEquals($before, $after);
    }

    /**
     * Test loading a non-existing file.
     *
     * @covers Lunr\Core\Configuration::loadFile
     */
    public function testLoadNonExistingFile(): void
    {
        $warning  = "include_once(conf.not_exists.inc.php): Failed to open stream: No such file or directory\n";
        $warning .= "WARNING: include_once(): Failed opening 'conf.not_exists.inc.php' for inclusion";
        $warning .= " (include_path='" . get_include_path() . "')";

        $this->expectWarning($warning);

        $before = $this->getReflectionPropertyValue('config');

        $this->class->loadFile('not_exists');

        $after = $this->getReflectionPropertyValue('config');

        $this->assertEquals($before, $after);
    }

    /**
     * Test that loading a file invalidates the cached size value.
     *
     * @runInSeparateProcess
     *
     * @covers Lunr\Core\Configuration::loadFile
     */
    public function testLoadFileInvalidatesSize(): void
    {
        $this->assertPropertyEquals('sizeInvalid', FALSE);

        $this->class->loadFile('correct');

        $this->assertPropertyEquals('sizeInvalid', TRUE);
    }

    /**
     * Test loading a correct config file.
     *
     * @runInSeparateProcess
     *
     * @depends Lunr\Core\Tests\ConfigurationArrayConstructorTest::testToArrayEqualsInput
     * @covers  Lunr\Core\Configuration::load_file
     */
    public function testDeprecatedLoadCorrectFile(): void
    {
        $this->class->load_file('correct');

        $this->config['load']        = [];
        $this->config['load']['one'] = 'Value';
        $this->config['load']['two'] = 'String';

        $this->assertEquals($this->config, $this->class->toArray());
    }

    /**
     * Test loading a correct config file.
     *
     * @runInSeparateProcess
     *
     * @depends Lunr\Core\Tests\ConfigurationArrayConstructorTest::testToArrayEqualsInput
     * @covers  Lunr\Core\Configuration::load_file
     */
    public function testDeprecatedLoadFileOverwritesValues(): void
    {
        $this->class->load_file('overwrite');

        $config                   = [];
        $config['test1']          = 'Value';
        $config['test2']          = [];
        $config['test2']['test3'] = 1;
        $config['test2']['test4'] = FALSE;

        $this->assertEquals($config, $this->class->toArray());
    }

    /**
     * Test loading a correct config file.
     *
     * @runInSeparateProcess
     *
     * @depends Lunr\Core\Tests\ConfigurationArrayConstructorTest::testToArrayEqualsInput
     * @covers  Lunr\Core\Configuration::load_file
     */
    public function testDeprecatedLoadFileMergesArrays(): void
    {
        $this->class->load_file('merge');

        $config                   = [];
        $config['test1']          = 'String';
        $config['test2']          = [];
        $config['test2']['test3'] = 1;
        $config['test2']['test4'] = FALSE;
        $config['test2']['test5'] = 'Value';

        $this->assertEquals($config, $this->class->toArray());
    }

    /**
     * Test loading an invalid config file.
     *
     * @covers Lunr\Core\Configuration::load_file
     */
    public function testDeprecatedLoadInvalidFile(): void
    {
        $before = $this->getReflectionPropertyValue('config');

        $this->class->load_file('not_array');

        $after = $this->getReflectionPropertyValue('config');

        $this->assertEquals($before, $after);
    }

    /**
     * Test loading a non-existing file.
     *
     * @covers Lunr\Core\Configuration::load_file
     */
    public function testDeprecatedLoadNonExistingFile(): void
    {
        $warning  = "include_once(conf.not_exists.inc.php): Failed to open stream: No such file or directory\n";
        $warning .= "WARNING: include_once(): Failed opening 'conf.not_exists.inc.php' for inclusion";
        $warning .= " (include_path='" . get_include_path() . "')";

        $this->expectWarning($warning);

        $before = $this->getReflectionPropertyValue('config');

        $this->class->load_file('not_exists');

        $after = $this->getReflectionPropertyValue('config');

        $this->assertEquals($before, $after);
    }

    /**
     * Test that loading a file invalidates the cached size value.
     *
     * @runInSeparateProcess
     *
     * @covers Lunr\Core\Configuration::load_file
     */
    public function testDeprecatedLoadFileInvalidatesSize(): void
    {
        $this->assertPropertyEquals('sizeInvalid', FALSE);

        $this->class->load_file('correct');

        $this->assertPropertyEquals('sizeInvalid', TRUE);
    }

}

?>
