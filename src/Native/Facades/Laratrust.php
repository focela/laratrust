<?php
/*
 * Copyright (c) 2024 Focela Technologies. All rights reserved.
 * Use of this source code is governed by a MIT style
 * license that can be found in the LICENSE file.
 */

namespace Focela\Laratrust\Native\Facades;

use Focela\Laratrust\Native\LaratrustBootstrapper;

class Laratrust
{
    /**
     * The Native Bootstraper instance.
     *
     * @var LaratrustBootstrapper
     */
    protected static $instance;

    /**
     * The Laratrust instance.
     *
     * @var \Focela\Laratrust\Laratrust
     */
    protected $laratrust;

    /**
     * Constructor.
     *
     * @param LaratrustBootstrapper $bootstrapper
     *
     * @return void
     */
    public function __construct(?LaratrustBootstrapper $bootstrapper = null)
    {
        if ($bootstrapper === null) {
            $bootstrapper = new LaratrustBootstrapper();
        }

        $this->laratrust = $bootstrapper->createLaratrust();
    }

    /**
     * Handle dynamic, static calls to the object.
     *
     * @param string $method
     * @param array  $args
     *
     * @return mixed
     */
    public static function __callStatic($method, $args)
    {
        $instance = static::instance()->getLaratrust();

        switch (count($args)) {
            case 0:
                return $instance->{$method}();
            case 1:
                return $instance->{$method}($args[0]);
            case 2:
                return $instance->{$method}($args[0], $args[1]);
            case 3:
                return $instance->{$method}($args[0], $args[1], $args[2]);
            case 4:
                return $instance->{$method}($args[0], $args[1], $args[2], $args[3]);
            default:
                return call_user_func_array([$instance, $method], $args);
        }
    }

    /**
     * Returns the Laratrust instance.
     *
     * @return \Focela\Laratrust\Laratrust
     */
    public function getLaratrust()
    {
        return $this->laratrust;
    }

    /**
     * Creates a new Native Bootstraper instance.
     *
     * @param LaratrustBootstrapper $bootstrapper
     *
     * @return LaratrustBootstrapper
     */
    public static function instance(?LaratrustBootstrapper $bootstrapper = null)
    {
        if (static::$instance === null) {
            static::$instance = new static($bootstrapper);
        }

        return static::$instance;
    }
}
