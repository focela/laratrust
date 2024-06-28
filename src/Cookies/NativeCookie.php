<?php
/*
 * Copyright (c) 2024 Focela Technologies. All rights reserved.
 * Use of this source code is governed by a MIT style
 * license that can be found in the LICENSE file.
 */

namespace Focela\Laratrust\Cookies;

class NativeCookie implements CookieInterface
{
    /**
     * The cookie options.
     *
     * @var array
     */
    protected $options = [
        'name'      => 'focela_laratrust',
        'domain'    => '',
        'path'      => '/',
        'secure'    => false,
        'http_only' => false,
    ];

    /**
     * Constructor.
     *
     * @param array|string $options
     *
     * @return void
     */
    public function __construct($options = [])
    {
        if (is_array($options)) {
            $this->options = array_merge($this->options, $options);
        } else {
            $this->options['name'] = $options;
        }
    }

    /**
     * @inheritdoc
     */
    public function put($value): void
    {
        $this->setCookie($value, $this->minutesToLifetime(2628000));
    }

    /**
     * Sets a PHP cookie.
     *
     * @param mixed  $value
     * @param int    $lifetime
     * @param string $path
     * @param string $domain
     * @param bool   $secure
     * @param bool   $httpOnly
     *
     * @return void
     */
    protected function setCookie($value, int $lifetime, ?string $path = null, ?string $domain = null, ?bool $secure = null, ?bool $httpOnly = null)
    {
        setcookie(
            $this->options['name'],
            json_encode($value),
            $lifetime,
            $path ?: $this->options['path'],
            $domain ?: $this->options['domain'],
            $secure ?: $this->options['secure'],
            $httpOnly ?: $this->options['http_only']
        );
    }

    /**
     * Takes a minute parameter (relative to now)
     * and converts it to a lifetime (unix timestamp).
     *
     * @param int $minutes
     *
     * @return int
     */
    protected function minutesToLifetime(int $minutes): int
    {
        return time() + ($minutes * 60);
    }

    /**
     * @inheritdoc
     */
    public function get()
    {
        return $this->getCookie();
    }

    /**
     * Returns a PHP cookie.
     *
     * @return mixed
     */
    protected function getCookie()
    {
        if (isset($_COOKIE[$this->options['name']])) {
            $value = $_COOKIE[$this->options['name']];

            if ($value) {
                return json_decode($value);
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function forget(): void
    {
        $this->setCookie(null, $this->minutesToLifetime(-2628000));
    }
}
