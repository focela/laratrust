<?php
/*
 * Copyright (c) 2024 Focela Technologies. All rights reserved.
 * Use of this source code is governed by a MIT style
 * license that can be found in the LICENSE file.
 */

namespace Focela\Laratrust\Native;

use Focela\Laratrust\Laratrust;
use Illuminate\Events\Dispatcher;
use Focela\Laratrust\Cookies\NativeCookie;
use Focela\Laratrust\Hashing\NativeHasher;
use Focela\Laratrust\Sessions\NativeSession;
use Symfony\Component\HttpFoundation\Request;
use Focela\Laratrust\Checkpoints\ThrottleCheckpoint;
use Focela\Laratrust\Roles\IlluminateRoleRepository;
use Focela\Laratrust\Users\IlluminateUserRepository;
use Focela\Laratrust\Checkpoints\ActivationCheckpoint;
use Focela\Laratrust\Reminders\IlluminateReminderRepository;
use Focela\Laratrust\Throttling\IlluminateThrottleRepository;
use Focela\Laratrust\Activations\IlluminateActivationRepository;
use Focela\Laratrust\Persistences\IlluminatePersistenceRepository;

class LaratrustBootstrapper
{
    /**
     * Configuration.
     *
     * @var array
     */
    protected $config;

    /**
     * The event dispatcher.
     *
     * @var \Illuminate\Contracts\Events\Dispatcher
     */
    protected $dispatcher;

    /**
     * Constructor.
     *
     * @param array $config
     *
     * @return void
     */
    public function __construct($config = null)
    {
        if (is_string($config)) {
            $this->config = new ConfigRepository($config);
        } else {
            $this->config = $config ?: new ConfigRepository();
        }
    }

    /**
     * Creates a laratrust instance.
     *
     * @return Laratrust
     */
    public function createLaratrust()
    {
        $persistence = $this->createPersistence();
        $users       = $this->createUsers();
        $roles       = $this->createRoles();
        $activations = $this->createActivations();
        $dispatcher  = $this->getEventDispatcher();

        $laratrust = new Laratrust(
            $persistence,
            $users,
            $roles,
            $activations,
            $dispatcher
        );

        $throttle = $this->createThrottling();

        $ipAddress = $this->getIpAddress();

        $checkpoints = $this->createCheckpoints($activations, $throttle, $ipAddress);

        foreach ($checkpoints as $key => $checkpoint) {
            $laratrust->addCheckpoint($key, $checkpoint);
        }

        $reminders = $this->createReminders($users);

        $laratrust->setActivationRepository($activations);

        $laratrust->setReminderRepository($reminders);

        $laratrust->setThrottleRepository($throttle);

        return $laratrust;
    }

    /**
     * Creates a persistences repository.
     *
     * @return IlluminatePersistenceRepository
     */
    protected function createPersistence()
    {
        $session = $this->createSession();

        $cookie = $this->createCookie();

        $model = $this->config['persistences']['model'];

        $single = $this->config['persistences']['single'];

        $users = $this->config['users']['model'];

        if (class_exists($users) && method_exists($users, 'setPersistencesModel')) {
            forward_static_call_array([$users, 'setPersistencesModel'], [$model]);
        }

        return new IlluminatePersistenceRepository($session, $cookie, $model, $single);
    }

    /**
     * Creates a session.
     *
     * @return NativeSession
     */
    protected function createSession()
    {
        return new NativeSession($this->config['session']);
    }

    /**
     * Creates a cookie.
     *
     * @return NativeCookie
     */
    protected function createCookie()
    {
        return new NativeCookie($this->config['cookie']);
    }

    /**
     * Creates a user repository.
     *
     * @return IlluminateUserRepository
     */
    protected function createUsers()
    {
        $hasher = $this->createHasher();

        $model = $this->config['users']['model'];

        $roles = $this->config['roles']['model'];

        $persistences = $this->config['persistences']['model'];

        if (class_exists($roles) && method_exists($roles, 'setUsersModel')) {
            forward_static_call_array([$roles, 'setUsersModel'], [$model]);
        }

        if (class_exists($persistences) && method_exists($persistences, 'setUsersModel')) {
            forward_static_call_array([$persistences, 'setUsersModel'], [$model]);
        }

        return new IlluminateUserRepository($hasher, $this->getEventDispatcher(), $model);
    }

    /**
     * Creates a hasher.
     *
     * @return NativeHasher
     */
    protected function createHasher()
    {
        return new NativeHasher();
    }

    /**
     * Returns the event dispatcher.
     *
     * @return \Illuminate\Contracts\Events\Dispatcher
     */
    protected function getEventDispatcher()
    {
        if (! $this->dispatcher) {
            $this->dispatcher = new Dispatcher();
        }

        return $this->dispatcher;
    }

    /**
     * Creates a role repository.
     *
     * @return IlluminateRoleRepository
     */
    protected function createRoles()
    {
        $model = $this->config['roles']['model'];

        $users = $this->config['users']['model'];

        if (class_exists($users) && method_exists($users, 'setRolesModel')) {
            forward_static_call_array([$users, 'setRolesModel'], [$model]);
        }

        return new IlluminateRoleRepository($model);
    }

    /**
     * Creates an activation repository.
     *
     * @return IlluminateActivationRepository
     */
    protected function createActivations()
    {
        $model = $this->config['activations']['model'];

        $expires = $this->config['activations']['expires'];

        return new IlluminateActivationRepository($model, $expires);
    }

    /**
     * Create a throttling repository.
     *
     * @return IlluminateThrottleRepository
     */
    protected function createThrottling()
    {
        $model = $this->config['throttling']['model'];

        foreach (['global', 'ip', 'user'] as $type) {
            ${"{$type}Interval"} = $this->config['throttling'][$type]['interval'];

            ${"{$type}Thresholds"} = $this->config['throttling'][$type]['thresholds'];
        }

        return new IlluminateThrottleRepository(
            $model,
            $globalInterval,
            $globalThresholds,
            $ipInterval,
            $ipThresholds,
            $userInterval,
            $userThresholds
        );
    }

    /**
     * Returns the client's ip address.
     *
     * @return string
     */
    protected function getIpAddress()
    {
        $request = Request::createFromGlobals();

        return $request->getClientIp();
    }

    /**
     * Create activation and throttling checkpoints.
     *
     * @param IlluminateActivationRepository $activations
     * @param IlluminateThrottleRepository   $throttle
     * @param string                         $ipAddress
     *
     * @throws \InvalidArgumentException
     *
     * @return array
     */
    protected function createCheckpoints(IlluminateActivationRepository $activations, IlluminateThrottleRepository $throttle, $ipAddress)
    {
        $activeCheckpoints = $this->config['checkpoints'];

        $activation = $this->createActivationCheckpoint($activations);

        $throttle = $this->createThrottleCheckpoint($throttle, $ipAddress);

        $checkpoints = [];

        foreach ($activeCheckpoints as $checkpoint) {
            if (! isset(${$checkpoint})) {
                throw new \InvalidArgumentException("Invalid checkpoint [{$checkpoint}] given.");
            }

            $checkpoints[$checkpoint] = ${$checkpoint};
        }

        return $checkpoints;
    }

    /**
     * Create an activation checkpoint.
     *
     * @param IlluminateActivationRepository $activations
     *
     * @return ActivationCheckpoint
     */
    protected function createActivationCheckpoint(IlluminateActivationRepository $activations)
    {
        return new ActivationCheckpoint($activations);
    }

    /**
     * Create a throttle checkpoint.
     *
     * @param IlluminateThrottleRepository $throttle
     * @param string                       $ipAddress
     *
     * @return ThrottleCheckpoint
     */
    protected function createThrottleCheckpoint(IlluminateThrottleRepository $throttle, $ipAddress)
    {
        return new ThrottleCheckpoint($throttle, $ipAddress);
    }

    /**
     * Create a reminder repository.
     *
     * @param IlluminateUserRepository $users
     *
     * @return IlluminateReminderRepository
     */
    protected function createReminders(IlluminateUserRepository $users)
    {
        $model = $this->config['reminders']['model'];

        $expires = $this->config['reminders']['expires'];

        return new IlluminateReminderRepository($users, $model, $expires);
    }
}
