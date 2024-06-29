<?php
/*
 * Copyright (c) 2024 Focela Technologies. All rights reserved.
 * Use of this source code is governed by a MIT style
 * license that can be found in the LICENSE file.
 */

namespace Focela\Laratrust\Tests\Sessions;

use Mockery as m;
use Illuminate\Session\Store;
use PHPUnit\Framework\TestCase;
use Focela\Laratrust\Sessions\IlluminateSession;

class IlluminateSessionTest extends TestCase
{
    /** @test */
    public function it_can_put_a_value_on_session()
    {
        $store = m::mock(Store::class);
        $store->shouldReceive('put')->with('foo', 'bar')->once();
        $store->shouldReceive('get')->once()->andReturn('bar');

        $session = new IlluminateSession($store, 'foo');

        $session->put('bar');

        $this->assertSame('bar', $session->get());
    }

    /** @test */
    public function it_can_get_a_value_from_session()
    {
        $store = m::mock(Store::class);
        $store->shouldReceive('get')->with('foo')->once()->andReturn('bar');

        $session = new IlluminateSession($store, 'foo');

        $this->assertSame('bar', $session->get());
    }

    /** @test */
    public function it_can_forget_a_value_from_the_session()
    {
        $store = m::mock(Store::class);
        $store->shouldReceive('forget')->with('foo')->once();
        $store->shouldReceive('get')->once()->andReturn(null);

        $session = new IlluminateSession($store, 'foo');

        $session->forget();

        $this->assertNull($session->get());
    }

    /**
     * @inheritdoc
     */
    protected function tearDown(): void
    {
        m::close();
    }
}
