<?php
/*
 * Copyright (c) 2024 Focela Technologies. All rights reserved.
 * Use of this source code is governed by a MIT style
 * license that can be found in the LICENSE file.
 */

namespace Focela\Laratrust\Tests\Activations;

use Mockery as m;
use PHPUnit\Framework\TestCase;
use Focela\Laratrust\Activations\EloquentActivation;

class EloquentActivationTest extends TestCase
{
    /**
     * The Activation Eloquent instance.
     *
     * @var EloquentActivation
     */
    protected $activation;

    /** @test */
    public function it_can_get_the_completed_attribute_as_a_boolean()
    {
        $this->activation->completed = 1;

        $this->assertTrue($this->activation->completed);
    }

    /** @test */
    public function it_can_get_the_activation_code_using_the_getter()
    {
        $this->activation->code = 'foo';

        $this->assertSame('foo', $this->activation->getCode());
    }

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $this->activation = new EloquentActivation();
    }

    /**
     * @inheritdoc
     */
    protected function tearDown(): void
    {
        $this->activation = null;

        m::close();
    }
}
