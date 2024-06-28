<?php
/*
 * Copyright (c) 2024 Focela Technologies. All rights reserved.
 * Use of this source code is governed by a MIT style
 * license that can be found in the LICENSE file.
 */

namespace Focela\Laratrust\Tests\Permissions;

use Mockery as m;
use PHPUnit\Framework\TestCase;
use Focela\Laratrust\Permissions\StrictPermissions;

class StrictPermissionsTest extends TestCase
{
    /** @test */
    public function permissions_can_inherit_from_secondary_permissions()
    {
        $permissions = new StrictPermissions(
            ['foo' => true, 'bar' => false, 'fred' => true],
            [
                ['bar' => true],
                ['qux'  => true],
                ['fred' => false],
            ]
        );

        $this->assertTrue($permissions->hasAccess('foo'));
        $this->assertTrue($permissions->hasAccess('qux'));
        $this->assertFalse($permissions->hasAccess('bar'));
        $this->assertFalse($permissions->hasAccess('fred'));
        $this->assertFalse($permissions->hasAccess(['foo', 'bar']));

        $this->assertTrue($permissions->hasAnyAccess(['foo', 'bar']));
        $this->assertFalse($permissions->hasAnyAccess(['bar', 'fred']));
    }

    /** @test */
    public function permissions_with_wildcards_can_be_used()
    {
        $permissions = new StrictPermissions(['foo.bar' => true, 'foo.qux' => false]);

        $this->assertTrue($permissions->hasAccess('foo*'));
        $this->assertFalse($permissions->hasAccess('foo'));

        $permissions = new StrictPermissions(['foo.*' => true]);

        $this->assertTrue($permissions->hasAccess('foo.bar'));
        $this->assertTrue($permissions->hasAccess('foo.qux'));
    }

    /** @test */
    public function permissions_as_class_names_can_be_used()
    {
        $permissions = new StrictPermissions(['Class@method1,method2' => true]);

        $this->assertTrue($permissions->hasAccess('Class@method1'));
        $this->assertTrue($permissions->hasAccess('Class@method2'));
    }

    /**
     * @inheritdoc
     */
    protected function tearDown(): void
    {
        m::close();
    }
}
