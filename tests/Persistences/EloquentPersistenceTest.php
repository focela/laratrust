<?php
/*
 * Copyright (c) 2024 Focela Technologies. All rights reserved.
 * Use of this source code is governed by a MIT style
 * license that can be found in the LICENSE file.
 */

namespace Focela\Laratrust\Tests\Persistences;

use Mockery as m;
use PHPUnit\Framework\TestCase;
use Illuminate\Database\Connection;
use Focela\Laratrust\Users\EloquentUser;
use Illuminate\Database\Query\Grammars\Grammar;
use Illuminate\Database\Query\Processors\Processor;
use Illuminate\Database\ConnectionResolverInterface;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Focela\Laratrust\Persistences\EloquentPersistence;

class EloquentPersistenceTest extends TestCase
{
    /**
     * The Persistence instance.
     *
     * @var EloquentPersistence
     */
    protected $persistence;

    /** @test */
    public function it_can_get_the_user_relationship()
    {
        $this->addMockConnection($this->persistence);

        $this->assertInstanceOf(BelongsTo::class, $this->persistence->user());
    }

    protected function addMockConnection($model)
    {
        $model->setConnectionResolver($resolver = m::mock(ConnectionResolverInterface::class));
        $resolver->shouldReceive('connection')->andReturn(m::mock(Connection::class)->makePartial());

        $model->getConnection()->shouldReceive('getQueryGrammar')->andReturn(m::mock(Grammar::class));
        $model->getConnection()->shouldReceive('getPostProcessor')->andReturn(m::mock(Processor::class));
    }

    /** @test */
    public function it_can_set_and_get_the_user_model_class_name()
    {
        $this->assertSame(EloquentUser::class, $this->persistence->getUsersModel());

        $this->persistence->setUsersModel('FooClass');

        $this->assertSame('FooClass', $this->persistence->getUsersModel());
    }

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $this->persistence = new EloquentPersistence();
    }

    /**
     * @inheritdoc
     */
    protected function tearDown(): void
    {
        $this->persistence = null;

        m::close();
    }
}
