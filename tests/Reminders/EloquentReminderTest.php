<?php
/*
 * Copyright (c) 2024 Focela Technologies. All rights reserved.
 * Use of this source code is governed by a MIT style
 * license that can be found in the LICENSE file.
 */

namespace Focela\Laratrust\Tests\Reminders;

use Mockery as m;
use PHPUnit\Framework\TestCase;
use Focela\Laratrust\Reminders\EloquentReminder;

class EloquentReminderTest extends TestCase
{
    /** @test */
    public function it_can_get_the_completed_attribute_as_a_boolean()
    {
        $reminder = new EloquentReminder();

        $reminder->completed = 1;

        $this->assertTrue($reminder->completed);
    }

    /**
     * @inheritdoc
     */
    protected function tearDown(): void
    {
        m::close();
    }
}
