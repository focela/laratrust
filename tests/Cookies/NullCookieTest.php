<?php
/*
 * Copyright (c) 2024 Focela Technologies. All rights reserved.
 * Use of this source code is governed by a MIT style
 * license that can be found in the LICENSE file.
 */

namespace Focela\Laratrust\Tests\Cookies;

use PHPUnit\Framework\TestCase;
use Focela\Laratrust\Cookies\NullCookie;

class NullCookieTest extends TestCase
{
    /**
     * The cookie instance.
     *
     * @var NullCookie
     */
    protected $cookie;

    /** @test */
    public function it_can_put_a_cookie()
    {
        $this->assertNull($this->cookie->put('cookie'));
    }

    /** @test */
    public function it_can_get_a_cookie()
    {
        $this->assertNull($this->cookie->get());
    }

    /** @test */
    public function it_can_forget_a_cookie()
    {
        $this->assertNull($this->cookie->forget());
    }

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $this->cookie = new NullCookie();
    }

    /**
     * @inheritdoc
     */
    protected function tearDown(): void
    {
        $this->cookie = null;
    }
}
