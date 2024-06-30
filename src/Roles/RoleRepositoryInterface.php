<?php
/*
 * Copyright (c) 2024 Focela Technologies. All rights reserved.
 * Use of this source code is governed by a MIT style
 * license that can be found in the LICENSE file.
 */

namespace Focela\Laratrust\Roles;

interface RoleRepositoryInterface
{
    /**
     * Finds a role by the given primary key.
     *
     * @param string $id
     *
     * @return RoleInterface|null
     */
    public function findById(string $id): ?RoleInterface;

    /**
     * Finds a role by the given slug.
     *
     * @param string $slug
     *
     * @return RoleInterface|null
     */
    public function findBySlug(string $slug): ?RoleInterface;

    /**
     * Finds a role by the given name.
     *
     * @param string $name
     *
     * @return RoleInterface|null
     */
    public function findByName(string $name): ?RoleInterface;
}
