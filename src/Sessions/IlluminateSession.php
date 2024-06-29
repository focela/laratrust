<?php
/*
 * Copyright (c) 2024 Focela Technologies. All rights reserved.
 * Use of this source code is governed by a MIT style
 * license that can be found in the LICENSE file.
 */

namespace Focela\Laratrust\Sessions;

use Illuminate\Session\Store as SessionStore;

class IlluminateSession implements SessionInterface
{
    /**
     * The session store object.
     *
     * @var SessionStore
     */
    protected $session;

    /**
     * The session key.
     *
     * @var string
     */
    protected $key = 'focela_laratrust';

    /**
     * Constructor.
     *
     * @param SessionStore $session
     * @param string       $key
     *
     * @return void
     */
    public function __construct(SessionStore $session, ?string $key = null)
    {
        $this->session = $session;

        $this->key = $key;
    }

    /**
     * @inheritdoc
     */
    public function put($value): void
    {
        $this->session->put($this->key, $value);
    }

    /**
     * @inheritdoc
     */
    public function get()
    {
        return $this->session->get($this->key);
    }

    /**
     * @inheritdoc
     */
    public function forget(): void
    {
        $this->session->forget($this->key);
    }
}
