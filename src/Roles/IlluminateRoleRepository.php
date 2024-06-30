<?php
/*
 * Copyright (c) 2024 Focela Technologies. All rights reserved.
 * Use of this source code is governed by a MIT style
 * license that can be found in the LICENSE file.
 */

namespace Focela\Laratrust\Roles;

use Focela\Support\Traits\RepositoryTrait;

class IlluminateRoleRepository implements RoleRepositoryInterface
{
    use RepositoryTrait;

    /**
     * The Eloquent role model FQCN.
     *
     * @var string
     */
    protected $model = EloquentRole::class;

    /**
     * Create a new Illuminate role repository.
     *
     * @param string $model
     *
     * @return void
     */
    public function __construct(?string $model = null)
    {
        $this->model = $model;
    }

    /**
     * @inheritdoc
     */
    public function findById(string $id): ?RoleInterface
    {
        return $this->createModel()->newQuery()->find($id);
    }

    /**
     * @inheritdoc
     */
    public function findBySlug(string $slug): ?RoleInterface
    {
        return $this->createModel()->newQuery()->where('slug', $slug)->first();
    }

    /**
     * @inheritdoc
     */
    public function findByName(string $name): ?RoleInterface
    {
        return $this->createModel()->newQuery()->where('name', $name)->first();
    }
}
