<?php

/**
 * This file contains the ConfigurationLoadFileTest class.
 *
 * SPDX-FileCopyrightText: Copyright 2011 M2mobi B.V., Amsterdam, The Netherlands
 * SPDX-FileCopyrightText: Copyright 2022 Move Agency Group B.V., Zwolle, The Netherlands
 * SPDX-FileCopyrightText: Copyright 2025 Framna Netherlands B.V., Zwolle, The Netherlands
 * SPDX-License-Identifier: MIT
 */

namespace Lunr\Core\Tests;

use PHPUnit\Framework\Attributes\BackupGlobals;

/**
 * This tests loading configuration files via the Configuration class.
 *
 * @covers \Lunr\Core\Configuration
 */
class ConfigurationLoadEnvironmentTest extends ConfigurationTestCase
{

    /**
     * TestCase Constructor.
     */
    public function setUp(): void
    {
        $this->setUpArray($this->constructTestArray());
    }

    /**
     * Test loading the environment is blocked for non-root config.
     */
    #[BackupGlobals(TRUE)]
    public function testLoadEnvironmentOnlyOnRoot(): void
    {
        $this->setUpArray($this->constructTestArray(), FALSE);

        $_ENV['TEST']     = 'Test1';
        $_ENV['LOAD_ONE'] = 'Value';
        $_ENV['LOAD_TWO'] = 'String';

        $this->class['test2']->loadEnvironment();

        $this->assertEquals($this->config, $this->class->toArray());
        $this->assertArrayEmpty($this->getReflectionPropertyValue('environmentOverride'));
    }

    /**
     * Test loading the environment correctly.
     */
    #[BackupGlobals(TRUE)]
    public function testLoadEnvironment(): void
    {
        $_ENV = [];

        $_ENV['TEST']     = 'Test1';
        $_ENV['LOAD_ONE'] = 'Value';
        $_ENV['LOAD_TWO'] = 'String';

        $this->class->loadEnvironment();

        $override['test'] = 'Test1';

        $override['load']['one'] = 'Value';
        $override['load']['two'] = 'String';

        $this->assertEquals($this->config, $this->class->toArray());
        $this->assertPropertyEquals('environmentOverride', $override);
    }

    /**
     * Test loading the environment correctly with a prefix.
     */
    #[BackupGlobals(TRUE)]
    public function testLoadEnvironmentWithPrefix(): void
    {
        $_ENV = [];

        $_ENV['TEST']                = 'Test1';
        $_ENV['LUNR_LOAD_ONE']       = 'Value';
        $_ENV['LUNR_LOAD_TWO']       = 'String';
        $_ENV['LUNR_LOAD_THREE_TWO'] = 'String';

        $this->class->loadEnvironment('Lunr');

        $override['load']['one'] = 'Value';
        $override['load']['two'] = 'String';

        $override['load']['three']['two'] = 'String';

        $this->assertEquals($this->config, $this->class->toArray());
        $this->assertPropertyEquals('environmentOverride', $override);
    }

}

?>
