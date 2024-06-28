<?php
/*
 * Copyright (c) 2024 Focela Technologies. All rights reserved.
 * Use of this source code is governed by a MIT style
 * license that can be found in the LICENSE file.
 */

namespace Focela\Laratrust\Permissions;

interface PermissibleInterface
{
    /**
     * Returns the Permissions instance.
     *
     * @return PermissionsInterface
     */
    public function getPermissionsInstance(): PermissionsInterface;

    /**
     * Adds a permission.
     *
     * @param string $permission
     * @param bool   $value
     *
     * @return PermissibleInterface
     */
    public function addPermission(string $permission, bool $value = true): PermissibleInterface;

    /**
     * Updates a permission.
     *
     * @param string $permission
     * @param bool   $value
     * @param bool   $create
     *
     * @return PermissibleInterface
     */
    public function updatePermission(string $permission, bool $value = true, bool $create = false): PermissibleInterface;

    /**
     * Removes a permission.
     *
     * @param string $permission
     *
     * @return PermissibleInterface
     */
    public function removePermission(string $permission): PermissibleInterface;
}
