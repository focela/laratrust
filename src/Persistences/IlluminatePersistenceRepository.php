<?php
/*
 * Copyright (c) 2024 Focela Technologies. All rights reserved.
 * Use of this source code is governed by a MIT style
 * license that can be found in the LICENSE file.
 */

namespace Focela\Laratrust\Persistences;

use Focela\Laratrust\Users\UserInterface;
use Focela\Support\Traits\RepositoryTrait;
use Focela\Laratrust\Cookies\CookieInterface;
use Focela\Laratrust\Sessions\SessionInterface;

class IlluminatePersistenceRepository implements PersistenceRepositoryInterface
{
    use RepositoryTrait;

    /**
     * Single session.
     *
     * @var bool
     */
    protected $single = false;

    /**
     * Session storage driver.
     *
     * @var SessionInterface
     */
    protected $session;

    /**
     * Cookie storage driver.
     *
     * @var CookieInterface
     */
    protected $cookie;

    /**
     * Model name.
     *
     * @var string
     */
    protected $model = EloquentPersistence::class;

    /**
     * Create a new Laratrust persistence repository.
     *
     * @param SessionInterface $session
     * @param CookieInterface  $cookie
     * @param string           $model
     * @param bool             $single
     *
     * @return void
     */
    public function __construct(SessionInterface $session, CookieInterface $cookie, ?string $model = null, bool $single = false)
    {
        $this->model = $model;

        $this->session = $session;

        $this->cookie = $cookie;

        $this->single = $single;
    }

    /**
     * @inheritdoc
     */
    public function findUserByPersistenceCode(string $code): ?UserInterface
    {
        $persistence = $this->findByPersistenceCode($code);

        return $persistence ? $persistence->user : null;
    }

    /**
     * @inheritdoc
     */
    public function findByPersistenceCode(string $code): ?PersistenceInterface
    {
        return $this->createModel()->newQuery()->where('code', $code)->first();
    }

    /**
     * @inheritdoc
     */
    public function persistAndRemember(PersistableInterface $persistable): bool
    {
        return $this->persist($persistable, true);
    }

    /**
     * @inheritdoc
     */
    public function persist(PersistableInterface $persistable, bool $remember = false): bool
    {
        if ($this->single) {
            $this->flush($persistable);
        }

        $code = $persistable->generatePersistenceCode();

        $this->session->put($code);

        if ($remember) {
            $this->cookie->put($code);
        }

        $persistence = $this->createModel();

        $persistence->{$persistable->getPersistableKey()} = $persistable->getPersistableId();

        $persistence->code = $code;

        return $persistence->save();
    }

    /**
     * @inheritdoc
     */
    public function flush(PersistableInterface $persistable, bool $forget = true): void
    {
        if ($forget) {
            $this->forget();
        }

        $relationship = $persistable->getPersistableRelationship();

        foreach ($persistable->{$relationship}()->get() as $persistence) {
            if ($persistence->code !== $this->check()) {
                $persistence->delete();
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function forget(): ?bool
    {
        $code = $this->check();

        if ($code === null) {
            return null;
        }

        $this->session->forget();
        $this->cookie->forget();

        return $this->remove($code);
    }

    /**
     * @inheritdoc
     */
    public function check(): ?string
    {
        if ($code = $this->session->get()) {
            return $code;
        }

        if ($code = $this->cookie->get()) {
            return $code;
        }

        return null;
    }

    /**
     * @inheritdoc
     */
    public function remove(string $code): ?bool
    {
        return $this->createModel()->newQuery()->where('code', $code)->delete();
    }
}
