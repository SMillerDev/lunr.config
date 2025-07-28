<?php

/**
 * This file contains the main configuration class,
 * holding all configuration values and managing
 * access to those values.
 *
 * SPDX-FileCopyrightText: Copyright 2011 M2mobi B.V., Amsterdam, The Netherlands
 * SPDX-FileCopyrightText: Copyright 2022 Move Agency Group B.V., Zwolle, The Netherlands
 * SPDX-FileCopyrightText: Copyright 2025 Framna Netherlands B.V., Zwolle, The Netherlands
 * SPDX-License-Identifier: MIT
 */

namespace Lunr\Core;

use ArrayAccess;
use Countable;
use Iterator;

/**
 * Configuration Class
 *
 * @implements ArrayAccess<int|string,mixed>
 * @implements Iterator<int|string,mixed>
 */
class Configuration implements ArrayAccess, Iterator, Countable
{

    /**
     * Configuration values
     * @var array<int|string,mixed>
     */
    private array $config;

    /**
     * Position of the array pointer
     * @var int
     */
    private int $position;

    /**
     * Size of the $config array
     * @var int
     */
    private int $size;

    /**
     * Whether the cached size is invalid (outdated)
     * @var bool
     */
    private bool $sizeInvalid;

    /**
     * Whether the object holds top-level config values or not
     * @var bool
     */
    private readonly bool $isRootConfig;

    /**
     * Set of keys that we attempted to load a config file for.
     * @var string[]
     */
    private array $loaded;

    /**
     * Constructor.
     *
     * @param array<int|string,mixed>|bool $bootstrap    Bootstrap config values, aka config values used before
     *                                                   the class has been instantiated.
     * @param bool                         $isRootConfig Whether the object holds top-level config values or not
     */
    public function __construct(array|bool $bootstrap = FALSE, bool $isRootConfig = TRUE)
    {
        if (!is_array($bootstrap))
        {
            $bootstrap = [];
        }

        if (!empty($bootstrap))
        {
            $bootstrap = $this->convertArrayToClass($bootstrap);
        }

        $this->config = $bootstrap;
        $this->rewind();
        $this->sizeInvalid  = TRUE;
        $this->isRootConfig = $isRootConfig;
        $this->loaded       = [];
        $this->count();
    }

    /**
     * Destructor.
     */
    public function __destruct()
    {
        unset($this->config);
        unset($this->position);
        unset($this->size);
        unset($this->sizeInvalid);
    }

    /**
     * Called when cloning the object.
     *
     * @return void
     */
    public function __clone(): void
    {
        foreach ($this->config as $key => $value)
        {
            if (!($value instanceof self))
            {
                continue;
            }

            $this[$key] = clone $value;
        }

        $this->count();
    }

    /**
     * Handle the case when the object is treated like a string.
     *
     * Pretend to be an Array.
     *
     * @return string
     */
    public function __toString(): string
    {
        return 'Array';
    }

    /**
     * Load a config file.
     *
     * @deprecated Use loadFile() instead
     *
     * @param string $identifier Identifier string for the config file to load.
     *                           e.g.: For conf.lunr.inc.php the identifier would be 'lunr'
     *
     * @return void
     */
    public function load_file(string $identifier): void
    {
        $this->loadFile($identifier);
    }

    /**
     * Load a config file.
     *
     * @param string $identifier Identifier string for the config file to load.
     *                           e.g.: For conf.lunr.inc.php the identifier would be 'lunr'
     *
     * @return void
     */
    public function loadFile(string $identifier): void
    {
        $config = $this->config;

        include_once 'conf.' . $identifier . '.inc.php';

        /**
         * Since we're including a config file here, it's conceivable that $config
         * might not stay an array. Ignore the phpstan check for that.
         *
         * @phpstan-ignore function.alreadyNarrowedType
         */
        if (!is_array($config))
        {
            $config = [];
            return;
        }

        if (!empty($config))
        {
            $config = $this->convertArrayToClass($config);
        }

        $this->config      = $config;
        $this->sizeInvalid = TRUE;

        $this->loaded[] = $identifier;
    }

    /**
     * Attempt to autoload a config file.
     *
     * @param mixed $identifier Identifier string for the config file to load.
     *                          e.g.: For conf.lunr.inc.php the identifier would be 'lunr'
     *
     * @return void
     */
    private function autoloadFile(mixed $identifier): void
    {
        if (!$this->isRootConfig)
        {
            return;
        }

        if (!is_string($identifier))
        {
            return;
        }

        if (isset($this->config[$identifier]) || in_array($identifier, $this->loaded))
        {
            return;
        }

        if (!stream_resolve_include_path('conf.' . $identifier . '.inc.php'))
        {
            $this->loaded[] = $identifier;
            return;
        }

        $this->loadFile($identifier);
    }

    /**
     * Load environment variables as configuration.
     *
     * @param string|null $prefix The environment prefix to use
     *
     * @return void
     */
    public function loadEnvironment(?string $prefix = NULL): void
    {
        if ($this->isRootConfig === FALSE)
        {
            return;
        }

        $envConfig = [];
        $prefix    = $prefix !== NULL ? strtolower($prefix) : $prefix;
        foreach ($_ENV as $key => $value)
        {
            $key = strtolower($key);
            if ($prefix !== NULL && str_starts_with($key, $prefix . '_') === FALSE)
            {
                continue;
            }

            $tmpConfig  = [];
            $components = array_reverse(explode('_', $key));
            foreach ($components as $i => $identifier)
            {
                if ($prefix !== NULL && $identifier === $prefix)
                {
                    unset($components[$i]);
                    continue;
                }

                if ($i === 0)
                {
                    $tmpConfig[$identifier] = $value;
                    continue;
                }

                $tmpConfig = [ $identifier => $tmpConfig ];
            }

            $envConfig = array_replace_recursive($envConfig, $tmpConfig);
        }

        $config = array_replace_recursive($this->toArray(), $envConfig);
        if ($config !== [])
        {
            $config = $this->convertArrayToClass($config);
        }

        $this->config      = $config;
        $this->sizeInvalid = TRUE;
    }

    /**
     * Convert an input array recursively into a Configuration class hierarchy.
     *
     * @param array<int|string,mixed> $array Input array
     *
     * @return array<int|string,mixed> An array with sub-arrays converted
     */
    private function convertArrayToClass(array $array): array
    {
        if (empty($array))
        {
            return [];
        }

        foreach ($array as $key => $value)
        {
            if (!is_array($value))
            {
                continue;
            }

            $array[$key] = new self($value, isRootConfig: FALSE);
        }

        return $array;
    }

    /**
     * Offset to set.
     *
     * Assigns a value to the specified offset.
     * (inherited from ArrayAccess)
     *
     * @param mixed $offset The offset to assign the value to
     * @param mixed $value  The value to set
     *
     * @return void
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        if (is_array($value))
        {
            $value = $this->convertArrayToClass($value);
        }

        if (is_null($offset))
        {
            $this->config[] = $value;
        }
        else
        {
            $this->config[$offset] = $value;
        }

        $this->sizeInvalid = TRUE;
    }

    /**
     * Whether an offset exists.
     *
     * Whether or not an offset exists.
     * This method is executed when using isset() or empty().
     * (inherited from ArrayAccess)
     *
     * @param mixed $offset An offset to check for
     *
     * @return bool TRUE on success, FALSE on failure
     */
    public function offsetExists(mixed $offset): bool
    {
        $this->autoloadFile($offset);

        return isset($this->config[$offset]);
    }

    /**
     * Offset to unset.
     *
     * (Inherited from ArrayAccess)
     *
     * @param mixed $offset The offset to unset
     *
     * @return void
     */
    public function offsetUnset(mixed $offset): void
    {
        unset($this->config[$offset]);
        $this->sizeInvalid = TRUE;
    }

    /**
     * Offset to retrieve.
     *
     * Returns the value at specified offset.
     * (Inherited from ArrayAccess)
     *
     * @param mixed $offset The offset to retrieve
     *
     * @return mixed The value of the requested offset or null if
     *               it doesn't exist.
     */
    public function offsetGet(mixed $offset): mixed
    {
        $this->autoloadFile($offset);

        return $this->config[$offset] ?? NULL;
    }

    /**
     * Convert class content to an array.
     *
     * @return array<int|string,mixed> Array of all config values
     */
    public function toArray(): array
    {
        $data = $this->config;
        foreach ($data as $key => $value)
        {
            if (!($value instanceof self))
            {
                continue;
            }

            $data[$key] = $value->toArray();
        }

        return $data;
    }

    /**
     * Rewinds back to the first element of the Iterator.
     *
     * (Inherited from Iterator)
     *
     * @return void
     */
    public function rewind(): void
    {
        reset($this->config);
        $this->position = 0;
    }

    /**
     * Return the current element.
     *
     * (Inherited from Iterator)
     *
     * @return mixed The current value of the config array
     */
    public function current(): mixed
    {
        return current($this->config);
    }

    /**
     * Return the key of the current element.
     *
     * (Inherited from Iterator)
     *
     * @return array-key|null Value on success, NULL on failure
     */
    public function key(): int|string|null
    {
        return key($this->config);
    }

    /**
     * Move forward to next element.
     *
     * (Inherited from Iterator)
     *
     * @return void
     */
    public function next(): void
    {
        next($this->config);
        ++$this->position;
    }

    /**
     * Checks if current position is valid.
     *
     * (Inherited from Iterator)
     *
     * @return bool TRUE on success, FALSE on failure
     */
    public function valid(): bool
    {
        $return = $this->current();
        if (($return === FALSE) && ($this->position + 1 <= $this->count()))
        {
            $return = TRUE;
        }

        return $return !== FALSE;
    }

    /**
     * Count elements of an object.
     *
     * (Inherited from Countable)
     *
     * @return int Size of the config array
     */
    public function count(): int
    {
        if ($this->sizeInvalid === TRUE)
        {
            $this->size        = count($this->config);
            $this->sizeInvalid = FALSE;
        }

        return $this->size;
    }

}

?>
