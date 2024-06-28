<?php
/*
 * Copyright (c) 2024 Focela Technologies. All rights reserved.
 * Use of this source code is governed by a MIT style
 * license that can be found in the LICENSE file.
 */

namespace Focela\Laratrust\Users;

interface UserInterface
{
    /**
     * Returns the user primary key.
     *
     * @return string
     */
    public function getUserId(): string;

    /**
     * Returns the user login.
     *
     * @return string
     */
    public function getUserLogin(): string;

    /**
     * Returns the user login attribute name.
     *
     * @return string
     */
    public function getUserLoginName(): string;

    /**
     * Returns the user password.
     *
     * @return string
     */
    public function getUserPassword(): string;
}
