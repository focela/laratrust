<?php
/*
 * Copyright (c) 2024 Focela Technologies. All rights reserved.
 * Use of this source code is governed by a MIT style
 * license that can be found in the LICENSE file.
 */

namespace Focela\Laratrust\Tests\Checkpoints;

use Mockery as m;
use PHPUnit\Framework\TestCase;
use Focela\Laratrust\Users\EloquentUser;
use Focela\Laratrust\Checkpoints\ActivationCheckpoint;
use Focela\Laratrust\Checkpoints\NotActivatedException;
use Focela\Laratrust\Activations\ActivationRepositoryInterface;
use Focela\Laratrust\Activations\IlluminateActivationRepository;

class ActivationCheckpointTest extends TestCase
{
    /**
     * The Activations repository instance.
     *
     * @var ActivationRepositoryInterface
     */
    protected $activations;

    /**
     * The Eloquent User instance.
     *
     * @var EloquentUser
     */
    protected $user;

    /**
     * The activation checkpoint.
     *
     * @var \Focela\Laratrust\Checkpoint\ActivationCheckpoint
     */
    protected $checkpoint;

    /** @test */
    public function an_activated_user_can_login()
    {
        $this->activations->shouldReceive('completed')->once()->andReturn(true);

        $status = $this->checkpoint->login($this->user);

        $this->assertTrue($status);
    }

    /** @test */
    public function an_exception_will_be_thrown_when_authenticating_a_non_activated_user()
    {
        $this->activations->shouldReceive('completed')->once()->andReturn(false);

        try {
            $this->checkpoint->check($this->user);
        } catch (NotActivatedException $e) {
            $this->assertInstanceOf(EloquentUser::class, $e->getUser());
        }
    }

    /** @test */
    public function can_return_true_when_fail_is_called()
    {
        $this->assertTrue($this->checkpoint->fail());
    }

    /** @test */
    public function an_exception_will_be_thrown_when_the_user_is_not_activated_and_determining_if_the_user_is_logged_in()
    {
        $this->expectException(NotActivatedException::class);

        $this->activations->shouldReceive('completed')->once();

        $this->checkpoint->check($this->user);
    }

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $this->activations = m::mock(IlluminateActivationRepository::class);
        $this->user        = m::mock(EloquentUser::class);
        $this->checkpoint  = new ActivationCheckpoint($this->activations);
    }

    /**
     * @inheritdoc
     */
    protected function tearDown(): void
    {
        $this->activations = null;
        $this->user        = null;
        $this->checkpoint  = null;

        m::close();
    }
}
