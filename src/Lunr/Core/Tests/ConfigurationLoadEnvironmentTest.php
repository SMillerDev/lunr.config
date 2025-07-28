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

        $this->assertEquals($this->config, $this->class->toArray());
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

        $this->config['test'] = 'Test1';

        $this->config['load']['one'] = 'Value';
        $this->config['load']['two'] = 'String';

        $this->assertEquals($this->config, $this->class->toArray());
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

        $this->config['load']['one'] = 'Value';
        $this->config['load']['two'] = 'String';

        $this->config['load']['three']['two'] = 'String';

        $this->assertEquals($this->config, $this->class->toArray());
    }

    /**
     * Test loading the environment variables overwrite only what's needed
     */
    #[BackupGlobals(TRUE)]
    public function testLoadEnvironmentOverwritesValues(): void
    {
        $_ENV = [];

        $_ENV['TEST1']       = 'Overwrite Value';
        $_ENV['TEST2_TEST3'] = 'Overwrite Array';

        $this->class->loadEnvironment();

        $config = [];

        $config['test1'] = 'Overwrite Value';
        $config['test2'] = [];

        $config['test2']['test3'] = 'Overwrite Array';
        $config['test2']['test4'] = FALSE;

        $this->assertEquals($config, $this->class->toArray());
    }

}

?>
