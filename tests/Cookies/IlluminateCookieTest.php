<?php
/*
 * Copyright (c) 2024 Focela Technologies. All rights reserved.
 * Use of this source code is governed by a MIT style
 * license that can be found in the LICENSE file.
 */

namespace Focela\Laratrust\Tests\Cookies;

use Mockery as m;
use Illuminate\Http\Request;
use PHPUnit\Framework\TestCase;
use Illuminate\Cookie\CookieJar;
use Symfony\Component\HttpFoundation\Cookie;
use Focela\Laratrust\Cookies\IlluminateCookie;

class IlluminateCookieTest extends TestCase
{
    /** @test */
    public function it_can_put_a_cookie()
    {
        $jar = new CookieJar();

        $request = m::mock(Request::class);
        $request->shouldReceive('cookie')->with('foo')->once()->andReturn('bar');

        $illuminateCookie = new IlluminateCookie($request, $jar, 'foo');

        $illuminateCookie->put('bar');

        $this->assertSame('bar', $illuminateCookie->get());
    }

    /** @test */
    public function it_can_get_a_cookie()
    {
        $jar = m::mock(CookieJar::class);
        $jar->shouldReceive('getQueuedCookies')->once()->andReturn([]);

        $request = m::mock(Request::class);
        $request->shouldReceive('cookie')->with('foo')->once()->andReturn('bar');

        $illuminateCookie = new IlluminateCookie($request, $jar, 'foo');

        $this->assertSame('bar', $illuminateCookie->get());
    }

    /** @test */
    public function it_can_get_a_queued_cookie()
    {
        $cookie = m::mock(Cookie::class);
        $cookie->shouldReceive('getValue')->andReturn('bar');

        $jar = m::mock(CookieJar::class);
        $jar->shouldReceive('getQueuedCookies')->once()->andReturn(['foo' => $cookie]);

        $request = m::mock(Request::class);

        $illuminateCookie = new IlluminateCookie($request, $jar, 'foo');

        $this->assertSame('bar', $illuminateCookie->get());
    }

    /** @test */
    public function it_can_forget_a_cookie()
    {
        $jar = new CookieJar();

        $request = m::mock(Request::class);
        $request->shouldReceive('cookie')->with('foo')->once()->andReturn(null);

        $illuminateCookie = new IlluminateCookie($request, $jar, 'foo');

        $illuminateCookie->put('bar');

        $illuminateCookie->forget();

        $this->assertNull($illuminateCookie->get());
    }

    /**
     * @inheritdoc
     */
    protected function tearDown(): void
    {
        m::close();
    }
}
