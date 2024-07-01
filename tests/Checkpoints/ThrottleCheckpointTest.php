<?php
/*
 * Copyright (c) 2024 Focela Technologies. All rights reserved.
 * Use of this source code is governed by a MIT style
 * license that can be found in the LICENSE file.
 */

namespace Focela\Laratrust\Tests\Checkpoints;

use Mockery as m;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;
use Focela\Laratrust\Users\EloquentUser;
use Focela\Laratrust\Checkpoints\ThrottleCheckpoint;
use Focela\Laratrust\Checkpoints\ThrottleCheckpoints;
use Focela\Laratrust\Checkpoints\ThrottlingException;
use Focela\Laratrust\Throttling\ThrottleRepositoryInterface;
use Focela\Laratrust\Throttling\IlluminateThrottleRepository;

class ThrottleCheckpointTest extends TestCase
{
    /**
     * The Checkpoint instance.
     *
     * @var ThrottleCheckpoints
     */
    protected $checkpoint;

    /**
     * The Eloquent User instance.
     *
     * @var EloquentUser
     */
    protected $user;

    /**
     * The Users repository instance.
     *
     * @var ThrottleRepositoryInterface
     */
    protected $throttle;

    /** @test */
    public function can_login_the_user_without_being_throttled()
    {
        $this->throttle->shouldReceive('globalDelay')->once()->andReturn(0);
        $this->throttle->shouldReceive('ipDelay')->once();
        $this->throttle->shouldReceive('userDelay')->once()->andReturn(0);

        $status = $this->checkpoint->login($this->user);

        $this->assertTrue($status);
    }

    /** @test */
    public function can_check_if_the_user_is_being_throttled()
    {
        $this->throttle->shouldReceive('ipDelay')->once();

        $status = $this->checkpoint->check($this->user);

        $this->assertTrue($status);
    }

    /** @test */
    public function can_log_a_throttling_event()
    {
        $this->throttle->shouldReceive('globalDelay')->once();
        $this->throttle->shouldReceive('ipDelay')->once();
        $this->throttle->shouldReceive('userDelay')->once();
        $this->throttle->shouldReceive('log')->once();

        $this->checkpoint->fail($this->user);

        $this->assertTrue(true); // TODO: Add proper assertion later
    }

    /** @test */
    public function testWithIpAddress()
    {
        $this->throttle->shouldReceive('globalDelay')->once();
        $this->throttle->shouldReceive('ipDelay')->once();
        $this->throttle->shouldReceive('userDelay')->once();
        $this->throttle->shouldReceive('log')->once();

        $status = $this->checkpoint->fail($this->user);

        $this->assertTrue(true); // TODO: Add proper assertion later
    }

    /** @test */
    public function testFailedLogin()
    {
        $this->expectException(ThrottlingException::class);
        $this->expectExceptionMessage('Too many unsuccessful attempts have been made globally, logins are locked for another [10] second(s).');

        $this->throttle->shouldReceive('globalDelay')->once()->andReturn(10);

        $this->checkpoint->login($this->user);
    }

    /** @test */
    public function testThrowsExceptionWithIpDelay()
    {
        $this->expectException(ThrottlingException::class);
        $this->expectExceptionMessage('Suspicious activity has occured on your IP address and you have been denied access for another [10] second(s).');

        $this->throttle->shouldReceive('globalDelay')->once();
        $this->throttle->shouldReceive('ipDelay')->once()->andReturn(10);

        $this->checkpoint->fail($this->user);
    }

    /** @test */
    public function testThrowsExceptionWithUserDelay()
    {
        $this->expectException(ThrottlingException::class);
        $this->expectExceptionMessage('Too many unsuccessful login attempts have been made against your account. Please try again after another [10] second(s).');

        $this->throttle->shouldReceive('globalDelay')->once();
        $this->throttle->shouldReceive('ipDelay')->once()->andReturn(0);
        $this->throttle->shouldReceive('userDelay')->once()->andReturn(10);

        $this->checkpoint->fail($this->user);
    }

    /** @test */
    public function testGetThrottlingExceptionAttributes()
    {
        $this->throttle->shouldReceive('globalDelay')->once();
        $this->throttle->shouldReceive('ipDelay')->once()->andReturn(0);
        $this->throttle->shouldReceive('userDelay')->once()->andReturn(10);

        try {
            $this->checkpoint->fail($this->user);
        } catch (ThrottlingException $e) {
            $this->assertSame(10, $e->getDelay());
            $this->assertSame('user', $e->getType());
            $this->assertEqualsWithDelta(Carbon::now()->addSeconds(10), $e->getFree(), 3);
        }
    }

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $this->throttle   = m::mock(IlluminateThrottleRepository::class);
        $this->user       = m::mock(EloquentUser::class);
        $this->checkpoint = new ThrottleCheckpoint($this->throttle, '127.0.0.1');
    }

    /**
     * @inheritdoc
     */
    protected function tearDown(): void
    {
        $this->throttle   = null;
        $this->user       = null;
        $this->checkpoint = null;

        m::close();
    }
}
