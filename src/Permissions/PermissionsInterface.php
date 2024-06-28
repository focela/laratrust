<?php
/*
 * Copyright (c) 2024 Focela Technologies. All rights reserved.
 * Use of this source code is governed by a MIT style
 * license that can be found in the LICENSE file.
 */

namespace Focela\Laratrust\Permissions;

interface PermissionsInterface
{
    /**
     * Returns if access is available for all given permissions.
     *
     * @param array|string $permissions
     *
     * @return bool
     */
    public function hasAccess($permissions): bool;

    /**
     * Returns if access is available for any given permissions.
     *
     * @param array|string $permissions
     *
     * @return bool
     */
    public function hasAnyAccess($permissions): bool;
}
