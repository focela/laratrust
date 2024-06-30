<?php
/*
 * Copyright (c) 2024 Focela Technologies. All rights reserved.
 * Use of this source code is governed by a MIT style
 * license that can be found in the LICENSE file.
 */

namespace Focela\Laratrust\Tests\Reminders;

use Mockery as m;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;
use Illuminate\Database\Eloquent\Builder;
use Focela\Laratrust\Reminders\EloquentReminder;
use Focela\Laratrust\Users\IlluminateUserRepository;
use Focela\Laratrust\Reminders\IlluminateReminderRepository;

class IlluminateReminderRepositoryTest extends TestCase
{
    /**
     * The User Repository instance.
     *
     * @var IlluminateUserRepository
     */
    protected $users;

    /**
     * The Eloquent Builder instance.
     *
     * @var Builder
     */
    protected $query;

    /**
     * The Eloquent Reminder instance.
     *
     * @var EloquentReminder
     */
    protected $model;

    /**
     * The Reminder Repository instance.
     *
     * @var IlluminateReminderRepository
     */
    protected $reminders;

    /** @test */
    public function it_can_be_instantiated()
    {
        $reminders = new IlluminateReminderRepository($this->users, 'ReminderModelMock', 259200);

        $this->assertSame('ReminderModelMock', $reminders->getModel());
    }

    /** @test */
    public function it_can_create_a_reminder_code()
    {
        $this->model->shouldReceive('fill');
        $this->model->shouldReceive('setAttribute');
        $this->model->shouldReceive('save')->once();

        $user = $this->getUserMock();

        $reminder = $this->reminders->create($user);

        $this->assertInstanceOf(EloquentReminder::class, $reminder);
    }

    protected function getUserMock()
    {
        $user = m::mock('Focela\Laratrust\Users\EloquentUser');

        $user->shouldReceive('getUserId')->once()->andReturn(1);

        return $user;
    }

    /** @test */
    public function it_can_determine_if_a_reminder_exists()
    {
        $this->query->shouldReceive('where')->with('user_id', '1')->andReturnSelf();
        $this->query->shouldReceive('where')->with('completed', false)->andReturnSelf();
        $this->query->shouldReceive('where')->with('created_at', '>', m::type(Carbon::class))->andReturnSelf();
        $this->query->shouldReceive('first')->once();

        $user = $this->getUserMock();

        $status = $this->reminders->exists($user);

        $this->assertFalse($status);
    }

    /** @test */
    public function it_can_determine_if_a_reminder_exists_with_a_code()
    {
        $this->query->shouldReceive('where')->with('user_id', '1')->andReturnSelf();
        $this->query->shouldReceive('where')->with('completed', false)->andReturnSelf();
        $this->query->shouldReceive('where')->with('code', 'foobar')->andReturnSelf();
        $this->query->shouldReceive('where')->with('created_at', '>', m::type(Carbon::class))->andReturnSelf();
        $this->query->shouldReceive('first')->once();

        $user = $this->getUserMock();

        $status = $this->reminders->exists($user, 'foobar');

        $this->assertFalse($status);
    }

    /** @test */
    public function it_can_complete_a_reminder()
    {
        $user = $this->getUserMock();

        $this->model->shouldReceive('fill')->once();
        $this->model->shouldReceive('save')->once();

        $this->users->shouldReceive('validForUpdate')->once()->andReturn(true);
        $this->users->shouldReceive('update')->once()->andReturn($user);

        $this->query->shouldReceive('where')->with('user_id', '1')->andReturnSelf();
        $this->query->shouldReceive('where')->with('code', 'foobar')->andReturnSelf();
        $this->query->shouldReceive('where')->with('completed', false)->andReturnSelf();
        $this->query->shouldReceive('where')->with('created_at', '>', m::type(Carbon::class))->andReturnSelf();
        $this->query->shouldReceive('first')->once()->andReturn($this->model);

        $status = $this->reminders->complete($user, 'foobar', 'secret');

        $this->assertTrue($status);
    }

    /** @test */
    public function it_cannot_complete_a_reminder_that_does_not_exist()
    {
        $this->query->shouldReceive('where')->with('user_id', '1')->andReturnSelf();
        $this->query->shouldReceive('where')->with('code', 'foobar')->andReturnSelf();
        $this->query->shouldReceive('where')->with('completed', false)->andReturnSelf();
        $this->query->shouldReceive('where')->with('created_at', '>', m::type(Carbon::class))->andReturnSelf();
        $this->query->shouldReceive('first')->once()->andReturn(null);

        $user = $this->getUserMock();

        $status = $this->reminders->complete($user, 'foobar', 'secret');

        $this->assertFalse($status);
    }

    /** @test */
    public function it_cannot_complete_a_reminder_that_has_expired()
    {
        $this->users->shouldReceive('validForUpdate')->once()->andReturn(false);

        $this->query->shouldReceive('where')->with('user_id', '1')->andReturnSelf();
        $this->query->shouldReceive('where')->with('code', 'foobar')->andReturnSelf();
        $this->query->shouldReceive('where')->with('completed', false)->andReturnSelf();
        $this->query->shouldReceive('where')->with('created_at', '>', m::type(Carbon::class))->andReturnSelf();
        $this->query->shouldReceive('first')->once()->andReturn($this->model);

        $user = $this->getUserMock();

        $status = $this->reminders->complete($user, 'foobar', 'secret');

        $this->assertFalse($status);
    }

    /** @test */
    public function it_can_remove_expired_reminders()
    {
        $this->query->shouldReceive('where')->with('completed', false)->andReturnSelf();
        $this->query->shouldReceive('where')->with('created_at', '<', m::type(Carbon::class))->andReturnSelf();

        $this->query->shouldReceive('delete')->once()->andReturn(true);

        $status = $this->reminders->removeExpired();

        $this->assertTrue($status);
    }

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $this->users = m::mock(IlluminateUserRepository::class);

        $this->query = m::mock(Builder::class);

        $this->model = m::mock(EloquentReminder::class);
        $this->model->shouldReceive('newQuery')->andReturn($this->query);

        $this->reminders = m::mock('Focela\Laratrust\Reminders\IlluminateReminderRepository[createModel]', [$this->users]);
        $this->reminders->shouldReceive('createModel')->andReturn($this->model);
    }

    /**
     * @inheritdoc
     */
    protected function tearDown(): void
    {
        $this->users     = null;
        $this->query     = null;
        $this->model     = null;
        $this->reminders = null;

        m::close();
    }
}
