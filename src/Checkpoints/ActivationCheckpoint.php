<?php
/*
 * Copyright (c) 2024 Focela Technologies. All rights reserved.
 * Use of this source code is governed by a MIT style
 * license that can be found in the LICENSE file.
 */

namespace Focela\Laratrust\Checkpoints;

use Focela\Laratrust\Users\UserInterface;
use Focela\Laratrust\Activations\ActivationRepositoryInterface;

class ActivationCheckpoint implements CheckpointInterface
{
    use AuthenticatedCheckpoint;

    /**
     * The Activations repository instance.
     *
     * @var ActivationRepositoryInterface
     */
    protected $activations;

    /**
     * Constructor.
     *
     * @param ActivationRepositoryInterface $activations
     *
     * @return void
     */
    public function __construct(ActivationRepositoryInterface $activations)
    {
        $this->activations = $activations;
    }

    /**
     * @inheritdoc
     */
    public function login(UserInterface $user): bool
    {
        return $this->checkActivation($user);
    }

    /**
     * Checks the activation status of the given user.
     *
     * @param UserInterface $user
     *
     * @throws NotActivatedException
     *
     * @return bool
     */
    protected function checkActivation(UserInterface $user): bool
    {
        $completed = $this->activations->completed($user);

        if (! $completed) {
            $exception = new NotActivatedException('Your account has not been activated yet.');

            $exception->setUser($user);

            throw $exception;
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function check(UserInterface $user): bool
    {
        return $this->checkActivation($user);
    }
}
