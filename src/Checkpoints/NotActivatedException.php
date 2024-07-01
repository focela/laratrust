<?php
/*
 * Copyright (c) 2024 Focela Technologies. All rights reserved.
 * Use of this source code is governed by a MIT style
 * license that can be found in the LICENSE file.
 */

namespace Focela\Laratrust\Checkpoints;

use Focela\Laratrust\Users\UserInterface;

class NotActivatedException extends \RuntimeException
{
    /**
     * The user which caused the exception.
     *
     * @var UserInterface
     */
    protected $user;

    /**
     * Returns the user.
     *
     * @return UserInterface
     */
    public function getUser(): UserInterface
    {
        return $this->user;
    }

    /**
     * Sets the user associated with Laratrust (does not log in).
     *
     * @param UserInterface
     *
     * @return void
     */
    public function setUser(UserInterface $user): void
    {
        $this->user = $user;
    }
}
