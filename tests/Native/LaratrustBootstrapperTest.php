<?php
/*
 * Copyright (c) 2024 Focela Technologies. All rights reserved.
 * Use of this source code is governed by a MIT style
 * license that can be found in the LICENSE file.
 */

namespace Focela\Laratrust\Tests\Native;

use Focela\Laratrust\Laratrust;
use PHPUnit\Framework\TestCase;
use Focela\Laratrust\Native\LaratrustBootstrapper;

class LaratrustBootstrapperTest extends TestCase
{
    /** @test */
    public function it_can_be_instantiated()
    {
        $bootstrapper = new LaratrustBootstrapper();

        $laratrust = $bootstrapper->createLaratrust();

        $this->assertInstanceOf(Laratrust::class, $laratrust);
    }
}
