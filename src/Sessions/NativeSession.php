<?php
/*
 * Copyright (c) 2024 Focela Technologies. All rights reserved.
 * Use of this source code is governed by a MIT style
 * license that can be found in the LICENSE file.
 */

namespace Focela\Laratrust\Sessions;

class NativeSession implements SessionInterface
{
    /**
     * The session key.
     *
     * @var string
     */
    protected $key = 'focela_laratrust';

    /**
     * Constructor.
     *
     * @param string $key
     *
     * @return void
     */
    public function __construct(?string $key = null)
    {
        $this->key = $key;

        $this->startSession();
    }

    /**
     * Starts the session if it does not exist.
     *
     * @return void
     */
    protected function startSession(): void
    {
        // Check that the session hasn't already been started
        if (session_status() != PHP_SESSION_ACTIVE && ! headers_sent()) {
            session_start();
        }
    }

    /**
     * Called upon destruction of the native session handler.
     *
     * @return void
     */
    public function __destruct()
    {
        $this->writeSession();
    }

    /**
     * Writes the session.
     *
     * @return void
     */
    protected function writeSession(): void
    {
        session_write_close();
    }

    /**
     * @inheritdoc
     */
    public function put($value): void
    {
        $this->setSession($value);
    }

    /**
     * Interacts with the $_SESSION global to set a property on it.
     * The property is serialized initially.
     *
     * @param mixed $value
     *
     * @return void
     */
    protected function setSession($value): void
    {
        $_SESSION[$this->key] = serialize($value);
    }

    /**
     * @inheritdoc
     */
    public function get()
    {
        return $this->getSession();
    }

    /**
     * Unserializes a value from the session and returns it.
     *
     * @return mixed
     */
    protected function getSession()
    {
        if (isset($_SESSION[$this->key])) {
            $value = $_SESSION[$this->key];

            if ($value) {
                return unserialize($value);
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function forget(): void
    {
        $this->forgetSession();
    }

    /**
     * Forgets the Laratrust session from the global $_SESSION.
     *
     * @return void
     */
    protected function forgetSession(): void
    {
        if (isset($_SESSION[$this->key])) {
            unset($_SESSION[$this->key]);
        }
    }
}
