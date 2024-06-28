<?php
/*
 * Copyright (c) 2024 Focela Technologies. All rights reserved.
 * Use of this source code is governed by a MIT style
 * license that can be found in the LICENSE file.
 */

namespace Focela\Laratrust\Activations;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Focela\Laratrust\Users\UserInterface;
use Focela\Support\Traits\RepositoryTrait;

class IlluminateActivationRepository implements ActivationRepositoryInterface
{
    use RepositoryTrait;

    /**
     * The Activation model FQCN.
     *
     * @var string
     */
    protected $model = EloquentActivation::class;

    /**
     * The activation expiration time, in seconds.
     *
     * @var int
     */
    protected $expires = 259200;

    /**
     * Constructor.
     *
     * @param string $model
     * @param int    $expires
     *
     * @return void
     */
    public function __construct(string $model, int $expires)
    {
        $this->model = $model;

        $this->expires = $expires;
    }

    /**
     * @inheritdoc
     */
    public function create(UserInterface $user): ActivationInterface
    {
        $activation = $this->createModel();

        $code = $this->generateActivationCode();

        $activation->fill([
            'code' => $code,
        ]);

        $activation->user_id = $user->getUserId();

        $activation->save();

        return $activation;
    }

    /**
     * Returns the random string used for the activation code.
     *
     * @return string
     */
    protected function generateActivationCode(): string
    {
        return Str::random(32);
    }

    /**
     * @inheritdoc
     */
    public function complete(UserInterface $user, string $code): bool
    {
        $expires = $this->expires();

        $activation = $this
            ->createModel()
            ->newQuery()
            ->where('user_id', $user->getUserId())
            ->where('code', $code)
            ->where('completed', false)
            ->where('created_at', '>', $expires)
            ->first()
        ;

        if (! $activation) {
            return false;
        }

        $activation->fill([
            'completed'    => true,
            'completed_at' => Carbon::now(),
        ]);

        $activation->save();

        return true;
    }

    /**
     * Returns the expiration date.
     *
     * @return Carbon
     */
    protected function expires(): Carbon
    {
        return Carbon::now()->subSeconds($this->expires);
    }

    /**
     * @inheritdoc
     */
    public function completed(UserInterface $user): bool
    {
        $userId = $user->getUserId();

        return $this->createModel()->newQuery()->where('user_id', $userId)->where('completed', true)->exists();
    }

    /**
     * @inheritdoc
     */
    public function exists(UserInterface $user, ?string $code = null): bool
    {
        return (bool) $this->get($user, $code);
    }

    /**
     * @inheritdoc
     */
    public function get(UserInterface $user, ?string $code = null): ?ActivationInterface
    {
        $expires = $this->expires();

        return $this
            ->createModel()
            ->newQuery()
            ->where('user_id', $user->getUserId())
            ->where('completed', false)
            ->where('created_at', '>', $expires)
            ->when($code, function ($query, $code) {
                return $query->where('code', $code);
            })
            ->first()
        ;
    }

    /**
     * @inheritdoc
     */
    public function remove(UserInterface $user): bool
    {
        $userId = $user->getUserId();

        $activation = $this->createModel()->newQuery()->where('user_id', $userId)->where('completed', true)->first();

        if (! $activation) {
            return false;
        }

        return $activation->delete();
    }

    /**
     * @inheritdoc
     */
    public function removeExpired(): bool
    {
        $expires = $this->expires();

        return $this->createModel()->newQuery()->where('completed', false)->where('created_at', '<', $expires)->delete();
    }
}
