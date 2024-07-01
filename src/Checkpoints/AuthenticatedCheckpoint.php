<?php
/*
 * Copyright (c) 2024 Focela Technologies. All rights reserved.
 * Use of this source code is governed by a MIT style
 * license that can be found in the LICENSE file.
 */

namespace Focela\Laratrust\Checkpoints;

use Focela\Laratrust\Users\UserInterface;

trait AuthenticatedCheckpoint
{
    /**
     * @inheritdoc
     */
    public function fail(?UserInterface $user = null): bool
    {
        return true;
    }
}
