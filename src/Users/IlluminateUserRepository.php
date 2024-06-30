<?php
/*
 * Copyright (c) 2024 Focela Technologies. All rights reserved.
 * Use of this source code is governed by a MIT style
 * license that can be found in the LICENSE file.
 */

namespace Focela\Laratrust\Users;

use Carbon\Carbon;
use Focela\Support\Traits\EventTrait;
use Focela\Support\Traits\RepositoryTrait;
use Illuminate\Contracts\Events\Dispatcher;
use Focela\Laratrust\Hashing\HasherInterface;

class IlluminateUserRepository implements UserRepositoryInterface
{
    use EventTrait, RepositoryTrait;

    /**
     * The Hasher instance.
     *
     * @var HasherInterface
     */
    protected $hasher;

    /**
     * The User model FQCN.
     *
     * @var string
     */
    protected $model = EloquentUser::class;

    /**
     * Constructor.
     *
     * @param HasherInterface $hasher
     * @param Dispatcher      $dispatcher
     * @param string|null     $model
     *
     * @return void
     */
    public function __construct(HasherInterface $hasher, ?Dispatcher $dispatcher = null, ?string $model = null)
    {
        $this->hasher = $hasher;

        $this->dispatcher = $dispatcher;

        $this->model = $model;
    }

    /**
     * @inheritdoc
     */
    public function findByCredentials(array $credentials): ?UserInterface
    {
        if (empty($credentials)) {
            return null;
        }

        $instance = $this->createModel();

        $loginNames = $instance->getLoginNames();

        list($logins) = $this->parseCredentials($credentials, $loginNames);

        if (empty($logins)) {
            return null;
        }

        $query = $instance->newQuery();

        if (is_array($logins)) {
            foreach ($logins as $key => $value) {
                $query->where($key, $value);
            }
        } else {
            $query->whereNested(function ($query) use ($loginNames, $logins) {
                foreach ($loginNames as $index => $name) {
                    $method = $index === 0 ? 'where' : 'orWhere';

                    $query->{$method}($name, $logins);
                }
            });
        }

        return $query->first();
    }

    /**
     * Parses the given credentials to return logins, password and others.
     *
     * @param array $credentials
     * @param array $loginNames
     *
     * @throws \InvalidArgumentException
     *
     * @return array
     */
    protected function parseCredentials(array $credentials, array $loginNames): array
    {
        if (isset($credentials['password'])) {
            $password = $credentials['password'];

            unset($credentials['password']);
        } else {
            $password = null;
        }

        $passedNames = array_intersect_key($credentials, array_flip($loginNames));

        if (count($passedNames) > 0) {
            $logins = [];

            foreach ($passedNames as $name => $value) {
                $logins[$name] = $credentials[$name];
                unset($credentials[$name]);
            }
        } elseif (isset($credentials['login'])) {
            $logins = $credentials['login'];
            unset($credentials['login']);
        } else {
            $logins = [];
        }

        return [$logins, $password, $credentials];
    }

    /**
     * @inheritdoc
     */
    public function findByPersistenceCode(string $code): ?UserInterface
    {
        return $this->createModel()
            ->newQuery()
            ->whereHas('persistences', function ($q) use ($code) {
                $q->where('code', $code);
            })
            ->first()
        ;
    }

    /**
     * @inheritdoc
     */
    public function recordLogin(UserInterface $user): bool
    {
        $user->last_login = Carbon::now();

        return (bool) $user->save();
    }

    /**
     * @inheritdoc
     */
    public function recordLogout(UserInterface $user): bool
    {
        return (bool) $user->save();
    }

    /**
     * @inheritdoc
     */
    public function validateCredentials(UserInterface $user, array $credentials): bool
    {
        return $this->hasher->check($credentials['password'], $user->password);
    }

    /**
     * @inheritdoc
     */
    public function validForCreation(array $credentials): bool
    {
        return $this->validateUser($credentials);
    }

    /**
     * Validates the user.
     *
     * @param array $credentials
     * @param int   $id
     *
     * @throws \InvalidArgumentException
     *
     * @return bool
     */
    protected function validateUser(array $credentials, ?int $id = null): bool
    {
        $instance = $this->createModel();

        $loginNames = $instance->getLoginNames();

        // We will simply parse credentials that check logins and passwords
        list($logins, $password) = $this->parseCredentials($credentials, $loginNames);

        if ($id === null) {
            if (empty($logins)) {
                throw new \InvalidArgumentException('No [login] credential was passed.');
            }

            if (empty($password)) {
                throw new \InvalidArgumentException('You have not passed a [password].');
            }
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function validForUpdate($user, array $credentials): bool
    {
        if ($user instanceof UserInterface) {
            $user = $user->getUserId();
        }

        return $this->validateUser($credentials, $user);
    }

    /**
     * @inheritdoc
     */
    public function create(array $credentials, ?\Closure $callback = null): ?UserInterface
    {
        $user = $this->createModel();

        $this->fireEvent('laratrust.user.creating', compact('user', 'credentials'));

        $this->fill($user, $credentials);

        if ($callback) {
            $result = $callback($user);

            if ($result === false) {
                return null;
            }
        }

        $user->save();

        $this->fireEvent('laratrust.user.created', compact('user', 'credentials'));

        return $user;
    }

    /**
     * Fills a user with the given credentials, intelligently.
     *
     * @param UserInterface $user
     * @param array         $credentials
     *
     * @return void
     */
    public function fill(UserInterface $user, array $credentials): void
    {
        $this->fireEvent('laratrust.user.filling', compact('user', 'credentials'));

        $loginNames = $user->getLoginNames();

        list($logins, $password, $attributes) = $this->parseCredentials($credentials, $loginNames);

        if (is_array($logins)) {
            $user->fill($logins);
        } else {
            $loginName = reset($loginNames);

            $user->fill([
                $loginName => $logins,
            ]);
        }

        $user->fill($attributes);

        if (isset($password)) {
            $password = $this->hasher->hash($password);

            $user->fill([
                'password' => $password,
            ]);
        }

        $this->fireEvent('laratrust.user.filled', compact('user', 'credentials'));
    }

    /**
     * @inheritdoc
     */
    public function update($user, array $credentials): UserInterface
    {
        if (! $user instanceof UserInterface) {
            $user = $this->findById($user);
        }

        $this->fireEvent('laratrust.user.updating', compact('user', 'credentials'));

        $this->fill($user, $credentials);

        $user->save();

        $this->fireEvent('laratrust.user.updated', compact('user', 'credentials'));

        return $user;
    }

    /**
     * @inheritdoc
     */
    public function findById(string $id): ?UserInterface
    {
        return $this->createModel()->newQuery()->find($id);
    }

    /**
     * Returns the hasher instance.
     *
     * @return HasherInterface
     */
    public function getHasher(): HasherInterface
    {
        return $this->hasher;
    }

    /**
     * Sets the hasher instance.
     *
     * @param HasherInterface $hasher
     *
     * @return void
     */
    public function setHasher(HasherInterface $hasher): void
    {
        $this->hasher = $hasher;
    }
}
