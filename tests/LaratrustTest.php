<?php
/*
 * Copyright (c) 2024 Focela Technologies. All rights reserved.
 * Use of this source code is governed by a MIT style
 * license that can be found in the LICENSE file.
 */

namespace Focela\Laratrust\Tests;

use Mockery as m;
use Focela\Laratrust\Laratrust;
use PHPUnit\Framework\TestCase;
use Focela\Laratrust\Roles\EloquentRole;
use Focela\Laratrust\Users\EloquentUser;
use Illuminate\Contracts\Events\Dispatcher;
use Focela\Laratrust\Roles\RoleRepositoryInterface;
use Focela\Laratrust\Users\UserRepositoryInterface;
use Focela\Laratrust\Activations\ActivationInterface;
use Focela\Laratrust\Checkpoints\CheckpointInterface;
use Focela\Laratrust\Reminders\ReminderRepositoryInterface;
use Focela\Laratrust\Throttling\ThrottleRepositoryInterface;
use Focela\Laratrust\Activations\ActivationRepositoryInterface;
use Focela\Laratrust\Persistences\PersistenceRepositoryInterface;

class LaratrustTest extends TestCase
{
    /**
     * The Illuminate Events Dispatcher instance.
     *
     * @var Dispatcher
     */
    protected $dispatcher;

    /**
     * The Laratrust instance.
     *
     * @var Laratrust
     */
    protected $laratrust;

    /**
     * The Users repository instance.
     *
     * @var UserRepositoryInterface
     */
    protected $users;

    /**
     * The Roles repository instance.
     *
     * @var RoleRepositoryInterface
     */
    protected $roles;

    /**
     * The Activations repository instance.
     *
     * @var ActivationRepositoryInterface
     */
    protected $activations;

    /**
     * The Persistences repository instance.
     *
     * @var PersistenceRepositoryInterface
     */
    protected $persistences;

    /**
     * The Eloquent User instance.
     *
     * @var EloquentUser
     */
    protected $user;

    /** @test */
    public function it_can_register_a_valid_user()
    {
        $this->users->shouldReceive('validForCreation')->once()->andReturn(true);
        $this->users->shouldReceive('create')->once()->andReturn($this->user);

        $credentials = [
            'email'    => 'foo@example.com',
            'password' => 'secret',
        ];

        $this->dispatcher->shouldReceive('dispatch')->once()
            ->with('laratrust.registering', [$credentials])
        ;

        $this->dispatcher->shouldReceive('dispatch')->once()
            ->with('laratrust.registered', $this->user)
        ;

        $result = $this->laratrust->register($credentials);

        $this->assertSame($result, $this->user);
    }

    /** @test */
    public function it_can_register_and_activate_a_valid_user()
    {
        $this->users->shouldReceive('validForCreation')->once()->andReturn(true);
        $this->users->shouldReceive('create')->once()->andReturn($this->user);

        $activation = m::mock(ActivationInterface::class);
        $activation->shouldReceive('getCode')->once()->andReturn('a_random_code');

        $this->activations->shouldReceive('create')->once()->andReturn($activation);
        $this->activations->shouldReceive('complete')->once()->andReturn(true);

        $this->dispatcher->shouldReceive('dispatch')->times(4);

        $result = $this->laratrust->registerAndActivate([
            'email'    => 'foo@example.com',
            'password' => 'secret',
        ]);

        $this->assertSame($result, $this->user);
    }

    /** @test */
    public function it_will_not_register_an_invalid_user()
    {
        $this->users->shouldReceive('validForCreation')->once()->andReturn(false);

        $this->dispatcher->shouldReceive('dispatch')->once();

        $result = $this->laratrust->register([
            'email' => 'foo@example.com',
        ]);

        $this->assertFalse($result);
    }

    /** @test */
    public function it_can_activate_a_user_using_its_id()
    {
        $activation = m::mock(ActivationInterface::class);
        $activation->shouldReceive('getCode')->once()->andReturn('a_random_code');

        $this->users->shouldReceive('findById')->with('1')->once()->andReturn($this->user);

        $this->activations->shouldReceive('create')->once()->andReturn($activation);
        $this->activations->shouldReceive('complete')->once()->andReturn(true);

        $this->dispatcher->shouldReceive('dispatch')->twice();

        $this->assertTrue($this->laratrust->activate('1'));
    }

    /** @test */
    public function it_can_activate_a_user_using_its_instance()
    {
        $activation = m::mock(ActivationInterface::class);
        $activation->shouldReceive('getCode')->once()->andReturn('a_random_code');

        $this->activations->shouldReceive('create')->once()->andReturn($activation);
        $this->activations->shouldReceive('complete')->once()->andReturn(true);

        $this->dispatcher->shouldReceive('dispatch')->twice();

        $this->assertTrue($this->laratrust->activate($this->user));
    }

    /** @test */
    public function it_can_activate_a_user_using_its_credentials()
    {
        $credentials = [
            'login'    => 'foo@example.com',
            'password' => 'secret',
        ];

        $activation = m::mock(ActivationInterface::class);
        $activation->shouldReceive('getCode')->once()->andReturn('a_random_code');

        $this->users->shouldReceive('findByCredentials')->with($credentials)->once()->andReturn($this->user);

        $this->activations->shouldReceive('create')->once()->andReturn($activation);
        $this->activations->shouldReceive('complete')->once()->andReturn(true);

        $this->dispatcher->shouldReceive('dispatch')->twice();

        $this->assertTrue($this->laratrust->activate($credentials));
    }

    /** @test */
    public function it_can_check_if_the_user_is_logged_in()
    {
        $this->persistences->shouldReceive('check')->once()->andReturn('foobar');
        $this->persistences->shouldReceive('findUserByPersistenceCode')->with('foobar')->andReturn($this->user);

        $this->assertSame($this->user, $this->laratrust->check());
    }

    /** @test */
    public function it_can_check_if_the_user_is_logged_in_when_it_is_not()
    {
        $this->persistences->shouldReceive('check')->once()->andReturn('foobar');
        $this->persistences->shouldReceive('findUserByPersistenceCode')->with('foobar')->andReturn(null);

        $this->assertFalse($this->laratrust->check());
    }

    /** @test */
    public function it_can_force_the_check_if_the_user_is_logged_in()
    {
        $this->persistences->shouldReceive('check')->once();
        $this->persistences->shouldReceive('findUserByPersistenceCode')->with('foobar')->andReturn($this->user);

        $checkpoint = m::mock(CheckpointInterface::class);

        $this->laratrust->addCheckpoint('activation', $checkpoint);

        $valid = $this->laratrust->forceCheck();

        $this->assertFalse($valid);
    }

    public function testGuest1()
    {
        $this->persistences->shouldReceive('check')->once();

        $this->assertTrue($this->laratrust->guest());
    }

    public function testGuest2()
    {
        $this->laratrust->setUser($this->user);

        $this->assertFalse($this->laratrust->guest());
    }

    /** @test */
    public function it_can_authenticate_a_user_using_its_credentials()
    {
        $credentials = [
            'login'    => 'foo@example.com',
            'password' => 'secret',
        ];

        $this->persistences->shouldReceive('persist')->once();

        $this->users->shouldReceive('findByCredentials')->with($credentials)->once()->andReturn($this->user);
        $this->users->shouldReceive('validateCredentials')->once()->andReturn(true);
        $this->users->shouldReceive('recordLogin')->once()->andReturn(true);

        $this->dispatcher->shouldReceive('until')->once()
            ->with('laratrust.authenticating', [$credentials])
        ;

        $this->dispatcher->shouldReceive('dispatch')->once()
            ->with('laratrust.logging-in', $this->user)
        ;

        $this->dispatcher->shouldReceive('dispatch')->once()
            ->with('laratrust.logged-in', $this->user)
        ;

        $this->dispatcher->shouldReceive('dispatch')->once()
            ->with('laratrust.authenticated', $this->user)
        ;

        $this->assertSame($this->user, $this->laratrust->authenticate($credentials));
    }

    /** @test */
    public function it_can_authenticate_a_user_using_its_user_instance()
    {
        $this->persistences->shouldReceive('persist')->once();

        $this->users->shouldReceive('recordLogin')->once()->andReturn(true);

        $this->dispatcher->shouldReceive('until')->once()
            ->with('laratrust.authenticating', [$this->user])
        ;

        $this->dispatcher->shouldReceive('dispatch')->once()
            ->with('laratrust.logging-in', $this->user)
        ;

        $this->dispatcher->shouldReceive('dispatch')->once()
            ->with('laratrust.logged-in', $this->user)
        ;

        $this->dispatcher->shouldReceive('dispatch')->once()
            ->with('laratrust.authenticated', $this->user)
        ;

        $this->assertSame($this->user, $this->laratrust->authenticate($this->user));
    }

    /** @test */
    public function it_will_not_authenticate_a_user_with_invalid_credentials()
    {
        $this->users->shouldReceive('findByCredentials')->once();

        $this->dispatcher->shouldReceive('until')->once();

        $this->assertFalse($this->laratrust->authenticate([]));
    }

    /** @test */
    public function it_can_authenticate_and_remember()
    {
        $credentials = [
            'login'    => 'foo@example.com',
            'password' => 'secret',
        ];

        $this->persistences->shouldReceive('persist')->once();

        $this->users->shouldReceive('findByCredentials')->with($credentials)->once()->andReturn($this->user);
        $this->users->shouldReceive('validateCredentials')->once()->andReturn(true);
        $this->users->shouldReceive('recordLogin')->once()->andReturn(true);

        $this->dispatcher->shouldReceive('until')->once();
        $this->dispatcher->shouldReceive('dispatch')->times(3);

        $this->assertSame($this->user, $this->laratrust->authenticateAndRemember($credentials));
    }

    /** @test */
    public function it_can_authenticate_when_checkpoints_are_disabled()
    {
        $this->laratrust->disableCheckpoints();

        $this->persistences->shouldReceive('persist')->once();
        $this->users->shouldReceive('recordLogin')->once()->andReturn(true);

        $this->dispatcher->shouldReceive('until');
        $this->dispatcher->shouldReceive('dispatch');

        $this->assertSame($this->user, $this->laratrust->authenticate($this->user));
    }

    /** @test */
    public function it_cannot_authenticate_when_firing_an_event_fails()
    {
        $credentials = [
            'login'    => 'foo@example.com',
            'password' => 'secret',
        ];

        $this->dispatcher->shouldReceive('until')->once()->andReturn(false);

        $this->assertFalse($this->laratrust->authenticate($credentials));
    }

    /** @test */
    public function it_cannot_authenticate_when_a_checkpoint_fails()
    {
        $checkpoint = m::mock(CheckpointInterface::class);
        $checkpoint->shouldReceive('login')->andReturn(false);
        $this->laratrust->addCheckpoint('foobar', $checkpoint);

        $this->dispatcher->shouldReceive('until');
        $this->dispatcher->shouldReceive('dispatch');

        $this->assertFalse($this->laratrust->authenticate($this->user));
    }

    /** @test */
    public function it_cannot_authenticate_when_a_login_fails()
    {
        $this->persistences->shouldReceive('persist')->once();

        $this->users->shouldReceive('recordLogin')->once()->andReturn(false);

        $this->dispatcher->shouldReceive('until');
        $this->dispatcher->shouldReceive('dispatch');

        $this->assertFalse($this->laratrust->authenticate($this->user));
    }

    /** @test */
    public function it_can_set_the_user_instance_on_the_laratrust_class()
    {
        $this->laratrust->setUser($this->user);

        $this->assertSame($this->user, $this->laratrust->getUser());
    }

    /** @test */
    public function it_can_bypass_all_checkpoints()
    {
        $this->persistences->shouldReceive('check')->once()->andReturn('foobar');
        $this->persistences->shouldReceive('findUserByPersistenceCode')->with('foobar')->andReturn($this->user);

        $activationCheckpoint = m::mock(CheckpointInterface::class);
        $throttleCheckpoint   = m::mock(CheckpointInterface::class);

        $this->laratrust->addCheckpoint('activation', $activationCheckpoint);
        $this->laratrust->addCheckpoint('throttle', $throttleCheckpoint);

        $this->laratrust->bypassCheckpoints(function ($laratrust) {
            $this->assertNotNull($laratrust->check());
        });
    }

    /** @test */
    public function it_can_bypass_a_specific_endpoint()
    {
        $this->persistences->shouldReceive('check')->once()->andReturn('foobar');
        $this->persistences->shouldReceive('findUserByPersistenceCode')->with('foobar')->andReturn($this->user);

        $activationCheckpoint = m::mock(CheckpointInterface::class);

        $throttleCheckpoint = m::mock(CheckpointInterface::class);
        $throttleCheckpoint->shouldReceive('check')->once();

        $this->laratrust->addCheckpoint('activation', $activationCheckpoint);
        $this->laratrust->addCheckpoint('throttle', $throttleCheckpoint);

        $this->laratrust->bypassCheckpoints(function ($s) {
            $this->assertNotNull($s->check());
        }, ['activation']);
    }

    /** @test */
    public function it_can_get_the_checkpoint_status()
    {
        $this->laratrust->disableCheckpoints();

        $this->assertFalse($this->laratrust->checkpointsStatus());

        $this->laratrust->enableCheckpoints();

        $this->assertTrue($this->laratrust->checkpointsStatus());
    }

    /** @test */
    public function it_can_disable_all_checkpoints()
    {
        $this->assertTrue($this->laratrust->checkpointsStatus());

        $this->laratrust->disableCheckpoints();

        $this->assertFalse($this->laratrust->checkpointsStatus());

        $this->persistences->shouldReceive('check')->once()->andReturn('foobar');
        $this->persistences->shouldReceive('findUserByPersistenceCode')->with('foobar')->andReturn($this->user);

        $activationCheckpoint = m::mock(CheckpointInterface::class);
        $throttleCheckpoint   = m::mock(CheckpointInterface::class);

        $this->laratrust->addCheckpoint('activation', $activationCheckpoint);
        $this->laratrust->addCheckpoint('throttle', $throttleCheckpoint);

        $this->assertNotNull($this->laratrust->check());
    }

    /** @test */
    public function it_can_enable_all_checkpoints()
    {
        $this->persistences->shouldReceive('check')->once()->andReturn('foobar');
        $this->persistences->shouldReceive('findUserByPersistenceCode')->with('foobar')->andReturn($this->user);

        $this->assertTrue($this->laratrust->checkpointsStatus());

        $this->laratrust->disableCheckpoints();

        $this->assertFalse($this->laratrust->checkpointsStatus());

        $this->laratrust->enableCheckpoints();

        $this->assertTrue($this->laratrust->checkpointsStatus());

        $activationCheckpoint = m::mock(CheckpointInterface::class);
        $throttleCheckpoint   = m::mock(CheckpointInterface::class);

        $activationCheckpoint->shouldReceive('check')->once();

        $this->laratrust->addCheckpoint('activation', $activationCheckpoint);
        $this->laratrust->addCheckpoint('throttle', $throttleCheckpoint);

        $this->assertNotNull($this->laratrust->check());
    }

    /** @test */
    public function it_can_add_checkpoint_at_runtime()
    {
        $activationCheckpoint = m::mock(CheckpointInterface::class);

        $this->laratrust->addCheckpoint('activation', $activationCheckpoint);

        $this->assertCount(1, $this->laratrust->getCheckpoints());
        $this->assertArrayHasKey('activation', $this->laratrust->getCheckpoints());
    }

    /** @test */
    public function it_can_remove_checkpoint_at_runtime()
    {
        $activationCheckpoint = m::mock(CheckpointInterface::class);
        $throttleCheckpoint   = m::mock(CheckpointInterface::class);

        $this->laratrust->addCheckpoint('activation', $activationCheckpoint);
        $this->laratrust->addCheckpoint('throttle', $throttleCheckpoint);

        $this->laratrust->removeCheckpoint('activation');

        $this->assertCount(1, $this->laratrust->getCheckpoints());
        $this->assertArrayNotHasKey('activation', $this->laratrust->getCheckpoints());
    }

    /** @test */
    public function it_can_remove_checkpoints_at_runtime()
    {
        $activationCheckpoint = m::mock(CheckpointInterface::class);
        $throttleCheckpoint   = m::mock(CheckpointInterface::class);

        $this->laratrust->addCheckpoint('activation', $activationCheckpoint);
        $this->laratrust->addCheckpoint('throttle', $throttleCheckpoint);

        $this->laratrust->removeCheckpoints([
            'activation',
            'throttle',
        ]);

        $this->assertCount(0, $this->laratrust->getCheckpoints());
    }

    /** @test */
    public function the_check_checkpoint_will_be_invoked()
    {
        $this->persistences->shouldReceive('check')->once()->andReturn('foobar');
        $this->persistences->shouldReceive('findUserByPersistenceCode')->with('foobar')->andReturn($this->user);

        $throttleCheckpoint = m::mock(CheckpointInterface::class);
        $throttleCheckpoint->shouldReceive('check')->once()->andReturn(false);

        $this->laratrust->addCheckpoint('throttle', $throttleCheckpoint);

        $this->assertFalse($this->laratrust->check());
    }

    /** @test */
    public function the_login_checkpoint_will_be_invoked()
    {
        $this->dispatcher->shouldReceive('until')->once();

        $throttleCheckpoint = m::mock(CheckpointInterface::class);
        $throttleCheckpoint->shouldReceive('login')->once()->andReturn(false);

        $this->laratrust->addCheckpoint('throttle', $throttleCheckpoint);

        $this->assertFalse($this->laratrust->authenticate($this->user));
    }

    /** @test */
    public function the_fail_checkpoint_will_be_invoked()
    {
        $credentials = [
            'login'    => 'foo@example.com',
            'password' => 'secret',
        ];

        $this->dispatcher->shouldReceive('until')->once();

        $this->users->shouldReceive('findByCredentials')->with($credentials)->once();

        $throttleCheckpoint = m::mock(CheckpointInterface::class);
        $throttleCheckpoint->shouldReceive('fail')->once()->andReturn(false);

        $this->laratrust->addCheckpoint('throttle', $throttleCheckpoint);

        $this->assertFalse($this->laratrust->authenticate($credentials));
    }

    /** @test */
    public function it_can_login_with_a_valid_user()
    {
        $this->persistences->shouldReceive('persist')->once();

        $this->dispatcher->shouldReceive('dispatch')->twice();

        $this->users->shouldReceive('recordLogin')->once()->andReturn(true);

        $this->assertSame($this->user, $this->laratrust->login($this->user));
    }

    /** @test */
    public function it_will_not_login_with_an_invalid_user()
    {
        $this->persistences->shouldReceive('persist')->once();

        $this->dispatcher->shouldReceive('dispatch')->once();

        $this->users->shouldReceive('recordLogin')->once()->andReturn(false);

        $this->assertFalse($this->laratrust->login($this->user));
    }

    public function it_will_ensure_the_user_is_not_defined_when_logging_out()
    {
        $this->persistences->shouldReceive('persist')->once();
        $this->persistences->shouldReceive('forget')->once();

        $this->users->shouldReceive('recordLogin')->once();
        $this->users->shouldReceive('recordLogout')->once();

        $this->laratrust->login($this->user);
        $this->laratrust->logout($this->user);

        $this->assertNull($this->laratrust->getUser(false));
    }

    /** @test */
    public function it_can_logout_the_current_user()
    {
        $this->persistences->shouldReceive('check')->once()->andReturn('foobar');
        $this->persistences->shouldReceive('findUserByPersistenceCode')->with('foobar')->once()->andReturn($this->user);
        $this->persistences->shouldReceive('forget')->once();

        $this->users->shouldReceive('recordLogout')->once()->andReturn(true);

        $this->dispatcher->shouldReceive('dispatch')->twice();

        $this->assertTrue($this->laratrust->logout($this->user));
    }

    /** @test */
    public function it_can_logout_the_user_on_the_other_devices()
    {
        $this->persistences->shouldReceive('check')->once()->andReturn('foobar');
        $this->persistences->shouldReceive('findUserByPersistenceCode')->with('foobar')->once()->andReturn($this->user);
        $this->persistences->shouldReceive('flush')->once();

        $this->dispatcher->shouldReceive('dispatch')->twice();

        $this->users->shouldReceive('recordLogout')->once()->andReturn(true);

        $this->assertTrue($this->laratrust->logout($this->user, true));
    }

    /** @test */
    public function it_can_maintain_a_user_session_after_logging_out_another_user()
    {
        $currentUser = m::mock(EloquentUser::class);

        $this->persistences->shouldReceive('persist')->once();
        $this->persistences->shouldReceive('flush')->once()->with($this->user, false);

        $this->dispatcher->shouldReceive('dispatch')->times(4);

        $this->users->shouldReceive('recordLogin')->once()->andReturn(true);

        $this->laratrust->login($currentUser);

        $this->laratrust->logout($this->user);

        $this->assertSame($currentUser, $this->laratrust->getUser(false));
    }

    /** @test */
    public function it_can_logout_an_invalid_user()
    {
        $user = null;

        $this->persistences->shouldReceive('check')->once();

        $this->dispatcher->shouldReceive('dispatch')->twice();

        $this->assertTrue($this->laratrust->logout($user, true));
    }

    /** @test */
    public function it_can_create_a_basic_response()
    {
        $response = json_encode(['response']);

        $this->laratrust->creatingBasicResponse(function () use ($response) {
            return $response;
        });

        $this->assertSame($response, $this->laratrust->getBasicResponse());
    }

    /** @test */
    public function it_can_set_and_get_the_various_repositories()
    {
        $this->laratrust->setPersistenceRepository($persistence = m::mock(PersistenceRepositoryInterface::class));
        $this->laratrust->setUserRepository($users = m::mock(UserRepositoryInterface::class));
        $this->laratrust->setRoleRepository($roles = m::mock(RoleRepositoryInterface::class));
        $this->laratrust->setActivationRepository($activations = m::mock(ActivationRepositoryInterface::class));
        $this->laratrust->setReminderRepository($reminders = m::mock(ReminderRepositoryInterface::class));
        $this->laratrust->setThrottleRepository($throttling = m::mock(ThrottleRepositoryInterface::class));

        $this->assertSame($persistence, $this->laratrust->getPersistenceRepository());
        $this->assertSame($users, $this->laratrust->getUserRepository());
        $this->assertSame($roles, $this->laratrust->getRoleRepository());
        $this->assertSame($activations, $this->laratrust->getActivationRepository());
        $this->assertSame($reminders, $this->laratrust->getReminderRepository());
        $this->assertSame($throttling, $this->laratrust->getThrottleRepository());
    }

    /** @test */
    public function it_can_pass_method_calls_to_a_user_repository_directly()
    {
        $this->users->shouldReceive('findById')->once()->andReturn(m::mock(EloquentUser::class));

        $user = $this->laratrust->findById(1);

        $this->assertInstanceOf(EloquentUser::class, $user);
    }

    /** @test */
    public function it_can_pass_method_calls_to_a_user_repository_via_findUserBy()
    {
        $this->users->shouldReceive('findById')->once()->andReturn(m::mock(EloquentUser::class));

        $user = $this->laratrust->findUserById(1);

        $this->assertInstanceOf(EloquentUser::class, $user);
    }

    /** @test */
    public function it_can_pass_method_calls_to_a_role_repository_via_findRoleBy()
    {
        $this->roles->shouldReceive('findById')->once()->andReturn(m::mock(EloquentRole::class));

        $user = $this->laratrust->findRoleById(1);

        $this->assertInstanceOf(EloquentRole::class, $user);
    }

    /** @test */
    public function it_can_pass_methods_via_the_user_repository_when_a_user_is_logged_in()
    {
        $this->user->shouldReceive('hasAccess')->andReturn(true);

        $this->persistences->shouldReceive('check')->andReturn(true);
        $this->persistences->shouldReceive('findUserByPersistenceCode')->andReturn($this->user);

        $this->assertTrue($this->laratrust->hasAccess());
    }

    /** @test */
    public function an_exception_will_be_thrown_when_activating_an_invalid_user()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('No valid user was provided.');

        $this->laratrust->activate(20.00);
    }

    /** @test */
    public function an_exception_will_be_thrown_when_registering_with_an_invalid_closure()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('You must provide a closure or a boolean.');

        $this->laratrust->register([
            'email' => 'foo@example.com',
        ], 'invalid_closure');
    }

    /** @test */
    public function an_exception_will_be_thrown_when_calling_methods_which_are_only_available_when_a_user_is_logged_in()
    {
        $this->expectException(\BadMethodCallException::class);
        $this->expectExceptionMessage('Method Focela\Laratrust\Laratrust::getRoles() can only be called if a user is logged in.');

        $this->persistences->shouldReceive('check')->once()->andReturn(null);

        $this->laratrust->getRoles();
    }

    /** @test */
    public function an_exception_will_be_thrown_when_calling_invalid_methods()
    {
        $this->expectException(\BadMethodCallException::class);
        $this->expectExceptionMessage('Call to undefined method Focela\Laratrust\Laratrust::methodThatDoesntExist()');

        $this->laratrust->methodThatDoesntExist();
    }

    // /** @test */
    // public function an_exception_will_be_thrown_when_trying_to_get_the_basic_response()
    // {
    //     $this->expectException(RuntimeException::class);
    //     $this->expectExceptionMessage('Attempting basic auth after headers have already been sent.');

    //     $this->laratrust->getBasicResponse();
    // }

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $this->user = m::mock(EloquentUser::class);

        $this->persistences = m::mock(PersistenceRepositoryInterface::class);
        $this->users        = m::mock(UserRepositoryInterface::class);
        $this->roles        = m::mock(RoleRepositoryInterface::class);
        $this->activations  = m::mock(ActivationRepositoryInterface::class);
        $this->dispatcher   = m::mock(Dispatcher::class);

        $this->laratrust = new Laratrust(
            $this->persistences,
            $this->users,
            $this->roles,
            $this->activations,
            $this->dispatcher
        );
    }

    /**
     * @inheritdoc
     */
    protected function tearDown(): void
    {
        $this->user         = null;
        $this->laratrust    = null;
        $this->persistences = null;
        $this->users        = null;
        $this->roles        = null;
        $this->activations  = null;
        $this->dispatcher   = null;
        m::close();
    }
}
