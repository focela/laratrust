<?php
/*
 * Copyright (c) 2024 Focela Technologies. All rights reserved.
 * Use of this source code is governed by a MIT style
 * license that can be found in the LICENSE file.
 */

namespace Focela\Laratrust\Tests\Permissions;

use Mockery as m;
use PHPUnit\Framework\TestCase;
use Focela\Laratrust\Permissions\PermissionsTrait;
use Focela\Laratrust\Permissions\PermissionsInterface;

class PermissionsTraitTest extends TestCase
{
    /** @test */
    public function it_can_set_the_secondary_permissions_at_runtime()
    {
        $secondaryPermissions = [
            [
                'test1' => true,
            ],
            [
                'test1' => false,
            ],
        ];

        $permissions = new PermissionsStub([
            'test'  => true,
            'test2' => false,
        ], $secondaryPermissions);

        $secondaryPermissions[] = [
            'test2' => true,
        ];

        $permissions->setSecondaryPermissions($secondaryPermissions);

        $this->assertTrue($permissions->hasAccess('test'));
        $this->assertFalse($permissions->hasAccess('test', 'test1'));
        $this->assertFalse($permissions->hasAccess(['test', 'test1']));

        $this->assertTrue($permissions->hasAnyAccess('test'));
        $this->assertTrue($permissions->hasAnyAccess('test', 'test1'));
        $this->assertTrue($permissions->hasAnyAccess(['test', 'test1']));
        $this->assertFalse($permissions->hasAnyAccess(['test4', 'test5']));

        $this->assertSame($secondaryPermissions, $permissions->getSecondaryPermissions());
    }

    /** @test */
    public function permissions_with_wildcards_can_be_used()
    {
        $permissions = new PermissionsStub([
            'user.add'    => true,
            'user.remove' => false,
        ]);

        $this->assertTrue($permissions->hasAccess('user.*'));
    }

    /** @test */
    public function personal_permissions_take_priority_over_pattern_match()
    {
        $permissions = new PermissionsStub([
            'user.*'      => true,
            'user.delete' => false,
        ]);

        $this->assertTrue($permissions->hasAccess('user.*'));
        $this->assertTrue($permissions->hasAccess('user.test'));
        $this->assertFalse($permissions->hasAccess('user.delete'));
    }

    /** @test */
    public function permissions_as_class_names_can_be_used()
    {
        $permissions = new PermissionsStub([
            'Foo\Bar\Baz@add,view'      => true,
            'Foo\Bar\Baz@update,remove' => false,
        ]);

        $this->assertTrue($permissions->hasAccess('Foo\Bar\Baz@add'));
        $this->assertTrue($permissions->hasAccess('Foo\Bar\Baz@view'));

        $this->assertFalse($permissions->hasAccess('Foo\Bar\Baz@update'));
        $this->assertFalse($permissions->hasAccess('Foo\Bar\Baz@remove'));
    }

    /**
     * @inheritdoc
     */
    protected function tearDown(): void
    {
        m::close();
    }
}

class PermissionsStub implements PermissionsInterface
{
    use PermissionsTrait;

    protected function createPreparedPermissions(): array
    {
        $prepared = [];

        if (! empty($this->getSecondaryPermissions())) {
            foreach ($this->getSecondaryPermissions() as $permissions) {
                $this->preparePermissions($prepared, $permissions);
            }
        }

        if (! empty($this->getPermissions())) {
            $permissions = [];
            $this->preparePermissions($permissions, $this->getPermissions());
            $prepared = array_merge($prepared, $permissions);
        }

        return $prepared;
    }
}
