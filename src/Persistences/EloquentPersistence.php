<?php
/*
 * Copyright (c) 2024 Focela Technologies. All rights reserved.
 * Use of this source code is governed by a MIT style
 * license that can be found in the LICENSE file.
 */

namespace Focela\Laratrust\Persistences;

use Illuminate\Database\Eloquent\Model;
use Focela\Laratrust\Users\EloquentUser;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EloquentPersistence extends Model implements PersistenceInterface
{
    /**
     * The Users model FQCN.
     *
     * @var string
     */
    protected static $usersModel = EloquentUser::class;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'persistences';

    /**
     * Get the Users model FQCN.
     *
     * @return string
     */
    public static function getUsersModel(): string
    {
        return static::$usersModel;
    }

    /**
     * Set the Users model FQCN.
     *
     * @param string $usersModel
     *
     * @return void
     */
    public static function setUsersModel(string $usersModel): void
    {
        static::$usersModel = $usersModel;
    }

    /**
     * @inheritdoc
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(static::$usersModel);
    }
}
