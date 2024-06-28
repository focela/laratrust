<?php
/*
 * Copyright (c) 2024 Focela Technologies. All rights reserved.
 * Use of this source code is governed by a MIT style
 * license that can be found in the LICENSE file.
 */

namespace Focela\Laratrust\Permissions;

trait PermissibleTrait
{
    /**
     * The Permissions instance FQCN.
     *
     * @var string
     */
    protected static $permissionsClass = StrictPermissions::class;

    /**
     * The cached permissions instance for the given user.
     *
     * @var PermissionsInterface
     */
    protected $permissionsInstance;

    /**
     * Returns the permissions class name.
     *
     * @return string
     */
    public static function getPermissionsClass(): string
    {
        return static::$permissionsClass;
    }

    /**
     * Sets the permissions class name.
     *
     * @param string $permissionsClass
     *
     * @return void
     */
    public static function setPermissionsClass(string $permissionsClass): void
    {
        static::$permissionsClass = $permissionsClass;
    }

    /**
     * Sets permissions.
     *
     * @param array $permissions
     *
     * @return $this
     */
    public function setPermissions(array $permissions): self
    {
        $this->permissions = $permissions;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getPermissionsInstance(): PermissionsInterface
    {
        if ($this->permissionsInstance === null) {
            $this->permissionsInstance = $this->createPermissions();
        }

        return $this->permissionsInstance;
    }

    /**
     * Creates the permissions object.
     *
     * @return $this
     */
    abstract protected function createPermissions(): PermissionsInterface;

    /**
     * @inheritdoc
     */
    public function updatePermission(string $permission, bool $value = true, bool $create = false): PermissibleInterface
    {
        if (array_key_exists($permission, $this->getPermissions())) {
            $permissions = $this->getPermissions();

            $permissions[$permission] = $value;

            $this->permissions = $permissions;
        } elseif ($create) {
            $this->addPermission($permission, $value);
        }

        return $this;
    }

    /**
     * Returns the permissions.
     *
     * @return array
     */
    public function getPermissions(): array
    {
        return $this->permissions ?? [];
    }

    /**
     * @inheritdoc
     */
    public function addPermission(string $permission, bool $value = true): PermissibleInterface
    {
        if (! array_key_exists($permission, $this->getPermissions())) {
            $this->permissions = array_merge($this->getPermissions(), [$permission => $value]);
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function removePermission(string $permission): PermissibleInterface
    {
        if (array_key_exists($permission, $this->getPermissions())) {
            $permissions = $this->getPermissions();

            unset($permissions[$permission]);

            $this->permissions = $permissions;
        }

        return $this;
    }
}
