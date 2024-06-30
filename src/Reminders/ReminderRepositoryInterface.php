<?php
/*
 * Copyright (c) 2024 Focela Technologies. All rights reserved.
 * Use of this source code is governed by a MIT style
 * license that can be found in the LICENSE file.
 */

namespace Focela\Laratrust\Reminders;

use Illuminate\Database\Eloquent\Model;
use Focela\Laratrust\Users\UserInterface;

interface ReminderRepositoryInterface
{
    /**
     * Create a new reminder record and code.
     *
     * @param UserInterface $user
     *
     * @return Model
     */
    public function create(UserInterface $user);

    /**
     * Gets the reminder for the given user.
     *
     * @param UserInterface $user
     * @param string|null   $code
     *
     * @return Model|null
     */
    public function get(UserInterface $user, ?string $code = null);

    /**
     * Check if a valid reminder exists.
     *
     * @param UserInterface $user
     * @param string|null   $code
     *
     * @return bool
     */
    public function exists(UserInterface $user, ?string $code = null): bool;

    /**
     * Complete reminder for the given user.
     *
     * @param UserInterface $user
     * @param string        $code
     * @param string        $password
     *
     * @return bool
     */
    public function complete(UserInterface $user, string $code, string $password): bool;

    /**
     * Remove expired reminder codes.
     *
     * @return bool
     */
    public function removeExpired(): bool;
}
