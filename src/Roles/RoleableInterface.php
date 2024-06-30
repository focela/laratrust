<?php
/*
 * Copyright (c) 2024 Focela Technologies. All rights reserved.
 * Use of this source code is governed by a MIT style
 * license that can be found in the LICENSE file.
 */

namespace Focela\Laratrust\Roles;

interface RoleableInterface
{
    /**
     * Returns all the associated roles.
     *
     * @return \IteratorAggregate
     */
    public function getRoles(): \IteratorAggregate;

    /**
     * Checks if the user is in the given role.
     *
     * @param mixed $role
     *
     * @return bool
     */
    public function inRole($role): bool;

    /**
     * Checks if the user is in any of the given roles.
     *
     * @param array $roles
     *
     * @return bool
     */
    public function inAnyRole(array $roles): bool;
}
