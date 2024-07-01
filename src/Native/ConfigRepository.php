<?php
/*
 * Copyright (c) 2024 Focela Technologies. All rights reserved.
 * Use of this source code is governed by a MIT style
 * license that can be found in the LICENSE file.
 */

namespace Focela\Laratrust\Native;

class ConfigRepository implements \ArrayAccess
{
    /**
     * The config file path.
     *
     * @var string
     */
    protected $file;

    /**
     * The config data.
     *
     * @var array
     */
    protected $config = [];

    /**
     * Constructor.
     *
     * @param string $file
     *
     * @return void
     */
    public function __construct($file = null)
    {
        $this->file = $file ?: __DIR__.'/../config/config.php';

        $this->load();
    }

    /**
     * Load the configuration file.
     *
     * @return void
     */
    protected function load()
    {
        $this->config = require $this->file;
    }

    /**
     * @inheritdoc
     */
    public function offsetExists($key): bool
    {
        return isset($this->config[$key]);
    }

    /**
     * @inheritdoc
     */
    public function offsetGet($key): mixed
    {
        return $this->config[$key];
    }

    /**
     * @inheritdoc
     */
    public function offsetSet($key, $value): void
    {
        $this->config[$key] = $value;
    }

    /**
     * @inheritdoc
     */
    public function offsetUnset($key): void
    {
        unset($this->config[$key]);
    }
}
