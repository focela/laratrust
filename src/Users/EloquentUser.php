<?php

/*
 * Copyright (c) 2024 Focela Technologies. All rights reserved.
 * Use of this source code is governed by a MIT style
 * license that can be found in the LICENSE file.
 */

namespace Focela\Laratrust\Users;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Focela\Laratrust\Roles\EloquentRole;
use Focela\Laratrust\Roles\RoleInterface;
use Focela\Laratrust\Roles\RoleableInterface;
use Focela\Laratrust\Reminders\EloquentReminder;
use Focela\Laratrust\Throttling\EloquentThrottle;
use Focela\Laratrust\Permissions\PermissibleTrait;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Focela\Laratrust\Activations\EloquentActivation;
use Focela\Laratrust\Permissions\PermissibleInterface;
use Focela\Laratrust\Permissions\PermissionsInterface;
use Focela\Laratrust\Persistences\EloquentPersistence;
use Focela\Laratrust\Persistences\PersistableInterface;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class EloquentUser extends Model implements PermissibleInterface, PersistableInterface, RoleableInterface, UserInterface
{
    use PermissibleTrait;

    /**
     * The Roles model FQCN.
     *
     * @var string
     */
    protected static $rolesModel = EloquentRole::class;

    /**
     * The Persistences model FQCN.
     *
     * @var string
     */
    protected static $persistencesModel = EloquentPersistence::class;

    /**
     * The Activations model FQCN.
     *
     * @var string
     */
    protected static $activationsModel = EloquentActivation::class;

    /**
     * The Reminders model FQCN.
     *
     * @var string
     */
    protected static $remindersModel = EloquentReminder::class;

    /**
     * The Throttling model FQCN.
     *
     * @var string
     */
    protected static $throttlingModel = EloquentThrottle::class;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email',
        'password',
        'last_name',
        'first_name',
        'permissions',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'permissions' => 'json',
    ];

    /**
     * @inheritdoc
     */
    protected $hidden = [
        'password',
    ];

    /**
     * @inheritdoc
     */
    protected $persistableKey = 'user_id';

    /**
     * @inheritdoc
     */
    protected $persistableRelationship = 'persistences';

    /**
     * Array of login column names.
     *
     * @var array
     */
    protected $loginNames = ['email'];

    /**
     * Returns the roles model.
     *
     * @return string
     */
    public static function getRolesModel(): string
    {
        return static::$rolesModel;
    }

    /**
     * Sets the roles model.
     *
     * @param string $rolesModel
     *
     * @return void
     */
    public static function setRolesModel(string $rolesModel): void
    {
        static::$rolesModel = $rolesModel;
    }

    /**
     * Returns the persistences model.
     *
     * @return string
     */
    public static function getPersistencesModel()
    {
        return static::$persistencesModel;
    }

    /**
     * Sets the persistences model.
     *
     * @param string $persistencesModel
     *
     * @return void
     */
    public static function setPersistencesModel(string $persistencesModel): void
    {
        static::$persistencesModel = $persistencesModel;
    }

    /**
     * Returns the activation model.
     *
     * @return string
     */
    public static function getActivationsModel(): string
    {
        return static::$activationsModel;
    }

    /**
     * Sets the activations model.
     *
     * @param string $activationsModel
     *
     * @return void
     */
    public static function setActivationsModel(string $activationsModel): void
    {
        static::$activationsModel = $activationsModel;
    }

    /**
     * Returns the reminders model.
     *
     * @return string
     */
    public static function getRemindersModel(): string
    {
        return static::$remindersModel;
    }

    /**
     * Sets the reminders model.
     *
     * @param string $remindersModel
     *
     * @return void
     */
    public static function setRemindersModel(string $remindersModel): void
    {
        static::$remindersModel = $remindersModel;
    }

    /**
     * Returns the throttling model.
     *
     * @return string
     */
    public static function getThrottlingModel(): string
    {
        return static::$throttlingModel;
    }

    /**
     * Sets the throttling model.
     *
     * @param string $throttlingModel
     *
     * @return void
     */
    public static function setThrottlingModel(string $throttlingModel): void
    {
        static::$throttlingModel = $throttlingModel;
    }

    /**
     * Returns an array of login column names.
     *
     * @return array
     */
    public function getLoginNames(): array
    {
        return $this->loginNames;
    }

    /**
     * @inheritdoc
     */
    public function getRoles(): \IteratorAggregate
    {
        return $this->roles;
    }

    /**
     * @inheritdoc
     */
    public function inAnyRole(array $roles): bool
    {
        foreach ($roles as $role) {
            if ($this->inRole($role)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function inRole($role): bool
    {
        if ($role instanceof RoleInterface) {
            $roleId = $role->getRoleId();
        }

        foreach ($this->roles as $instance) {
            if ($role instanceof RoleInterface) {
                if ($instance->getRoleId() === $roleId) {
                    return true;
                }
            } else {
                if ($instance->getRoleId() == $role || $instance->getRoleSlug() == $role) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function generatePersistenceCode(): string
    {
        return Str::random(32);
    }

    /**
     * @inheritdoc
     */
    public function getUserId(): string
    {
        return $this->getKey();
    }

    /**
     * @inheritdoc
     */
    public function getPersistableId(): string
    {
        return $this->getKey();
    }

    /**
     * @inheritdoc
     */
    public function getPersistableKey(): string
    {
        return $this->persistableKey;
    }

    /**
     * @inheritdoc
     */
    public function setPersistableKey(string $key): void
    {
        $this->persistableKey = $key;
    }

    /**
     * @inheritdoc
     */
    public function getPersistableRelationship(): string
    {
        return $this->persistableRelationship;
    }

    /**
     * @inheritdoc
     */
    public function setPersistableRelationship(string $persistableRelationship): void
    {
        $this->persistableRelationship = $persistableRelationship;
    }

    /**
     * @inheritdoc
     */
    public function getUserLogin(): string
    {
        return $this->getAttribute($this->getUserLoginName());
    }

    /**
     * @inheritdoc
     */
    public function getUserLoginName(): string
    {
        return reset($this->loginNames);
    }

    /**
     * @inheritdoc
     */
    public function getUserPassword(): string
    {
        return $this->password;
    }

    /**
     * @inheritdoc
     */
    public function delete()
    {
        $isSoftDeletable = property_exists($this, 'forceDeleting');

        $isSoftDeleted = $isSoftDeletable && ! $this->forceDeleting;

        if ($this->exists && ! $isSoftDeleted) {
            $this->activations()->delete();
            $this->persistences()->delete();
            $this->reminders()->delete();
            $this->roles()->detach();
            $this->throttle()->delete();
        }

        return parent::delete();
    }

    /**
     * Returns the activations relationship.
     *
     * @return HasMany
     */
    public function activations(): HasMany
    {
        return $this->hasMany(static::$activationsModel, 'user_id');
    }

    /**
     * Returns the persistences relationship.
     *
     * @return HasMany
     */
    public function persistences(): HasMany
    {
        return $this->hasMany(static::$persistencesModel, 'user_id');
    }

    /**
     * Returns the reminders relationship.
     *
     * @return HasMany
     */
    public function reminders(): HasMany
    {
        return $this->hasMany(static::$remindersModel, 'user_id');
    }

    /**
     * Returns the roles relationship.
     *
     * @return BelongsToMany
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(static::$rolesModel, 'role_users', 'user_id', 'role_id')->withTimestamps();
    }

    /**
     * Returns the throttle relationship.
     *
     * @return HasMany
     */
    public function throttle(): HasMany
    {
        return $this->hasMany(static::$throttlingModel, 'user_id');
    }

    /**
     * Dynamically pass missing methods to the user.
     *
     * @param string $method
     * @param array  $parameters
     *
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        $methods = ['hasAccess', 'hasAnyAccess'];

        if (in_array($method, $methods)) {
            $permissions = $this->getPermissionsInstance();

            return call_user_func_array([$permissions, $method], $parameters);
        }

        return parent::__call($method, $parameters);
    }

    /**
     * Creates a permissions object.
     *
     * @return PermissionsInterface
     */
    protected function createPermissions(): PermissionsInterface
    {
        $userPermissions = $this->getPermissions();

        $rolePermissions = [];

        foreach ($this->roles as $role) {
            $rolePermissions[] = $role->getPermissions();
        }

        return new static::$permissionsClass($userPermissions, $rolePermissions);
    }
}
