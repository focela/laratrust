<?php
/*
 * Copyright (c) 2024 Focela Technologies. All rights reserved.
 * Use of this source code is governed by a MIT style
 * license that can be found in the LICENSE file.
 */

namespace Focela\Laratrust\Roles;

interface RoleInterface
{
    /**
     * Returns the users model.
     *
     * @return string
     */
    public static function getUsersModel(): string;

    /**
     * Sets the user model.
     *
     * @param string $usersModel
     *
     * @return void
     */
    public static function setUsersModel(string $usersModel): void;

    /**
     * Returns the role's primary key.
     *
     * @return int
     */
    public function getRoleId(): int;

    /**
     * Returns the role's slug.
     *
     * @return string
     */
    public function getRoleSlug(): string;

    /**
     * Returns all users for the role.
     *
     * @return \IteratorAggregate
     */
    public function getUsers(): \IteratorAggregate;
}
