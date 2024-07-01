<?php
/*
 * Copyright (c) 2024 Focela Technologies. All rights reserved.
 * Use of this source code is governed by a MIT style
 * license that can be found in the LICENSE file.
 */

namespace Focela\Laratrust\Checkpoints;

use Focela\Laratrust\Users\UserInterface;

interface CheckpointInterface
{
    /**
     * Checkpoint after a user is logged in. Return false to deny persistence.
     *
     * @param UserInterface $user
     *
     * @return bool
     */
    public function login(UserInterface $user): bool;

    /**
     * Checkpoint for when a user is currently stored in the session.
     *
     * @param UserInterface $user
     *
     * @return bool
     */
    public function check(UserInterface $user): bool;

    /**
     * Checkpoint for when a failed login attempt is logged. User is not always
     * passed and the result of the method will not affect anything, as the
     * login failed.
     *
     * @param UserInterface|null $user
     *
     * @return bool
     */
    public function fail(?UserInterface $user = null): bool;
}
