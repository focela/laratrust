<?php
/*
 * Copyright (c) 2024 Focela Technologies. All rights reserved.
 * Use of this source code is governed by a MIT style
 * license that can be found in the LICENSE file.
 */

namespace Focela\Laratrust\Tests\Permissions;

use Mockery as m;
use PHPUnit\Framework\TestCase;
use Focela\Laratrust\Permissions\PermissibleTrait;
use Focela\Laratrust\Permissions\StandardPermissions;
use Focela\Laratrust\Permissions\PermissibleInterface;
use Focela\Laratrust\Permissions\PermissionsInterface;

class PermissibleTraitTest extends TestCase
{
    protected $permissible;

    /** @test */
    public function it_can_set_and_get_the_permissions_class()
    {
        $this->permissible::setPermissionsClass(StandardPermissions::class);

        $this->assertSame(StandardPermissions::class, $this->permissible::getPermissionsClass());
    }

    /** @test */
    public function it_can_get_the_permissions_intance()
    {
        $this->permissible::setPermissionsClass(StandardPermissions::class);

        $this->assertInstanceOf(StandardPermissions::class, $this->permissible->getPermissionsInstance());
    }

    /** @test */
    public function it_can_add_permissions()
    {
        $this->permissible->addPermission('test');
        $this->permissible->addPermission('test1');

        $permissions = [
            'test'  => true,
            'test1' => true,
        ];

        $this->assertSame($permissions, $this->permissible->getPermissions());
    }

    /** @test */
    public function it_can_update_permissions()
    {
        $this->permissible->addPermission('test');
        $this->permissible->addPermission('test1');
        $this->permissible->updatePermission('test1', false);

        $permissions = [
            'test'  => true,
            'test1' => false,
        ];

        $this->assertSame($permissions, $this->permissible->getPermissions());
    }

    /** @test */
    public function it_can_create_or_update_permissions()
    {
        $this->permissible->addPermission('test1');
        $this->permissible->updatePermission('test2', false);

        $permissions = [
            'test1' => true,
        ];

        $this->assertSame($permissions, $this->permissible->getPermissions());

        $this->permissible = new PermissibleStub();

        $this->permissible->addPermission('test1');
        $this->permissible->updatePermission('test2', false, true);

        $permissions = [
            'test1' => true,
            'test2' => false,
        ];

        $this->assertSame($permissions, $this->permissible->getPermissions());
    }

    /** @test */
    public function it_can_remove_permissions()
    {
        $this->permissible->addPermission('test');
        $this->permissible->addPermission('test1');
        $this->permissible->removePermission('test1');

        $permissions = [
            'test' => true,
        ];

        $this->assertSame($permissions, $this->permissible->getPermissions());
    }

    /** @test */
    public function it_can_use_the_setter_and_getter()
    {
        $permissions = [
            'test' => true,
        ];

        $this->permissible->setPermissions($permissions);

        $this->assertSame($permissions, $this->permissible->getPermissions());
    }

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $this->permissible = new PermissibleStub();
    }

    /**
     * @inheritdoc
     */
    protected function tearDown(): void
    {
        $this->permissible = null;

        m::close();
    }
}

class PermissibleStub implements PermissibleInterface
{
    use PermissibleTrait;

    protected $permissions = [];

    protected function createPermissions(): PermissionsInterface
    {
        return new static::$permissionsClass($this->getPermissions());
    }
}
