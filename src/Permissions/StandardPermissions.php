<?php
/*
 * Copyright (c) 2024 Focela Technologies. All rights reserved.
 * Use of this source code is governed by a MIT style
 * license that can be found in the LICENSE file.
 */

namespace Focela\Laratrust\Permissions;

class StandardPermissions implements PermissionsInterface
{
    use PermissionsTrait;

    /**
     * @inheritdoc
     */
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
