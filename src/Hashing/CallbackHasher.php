<?php
/*
 * Copyright (c) 2024 Focela Technologies. All rights reserved.
 * Use of this source code is governed by a MIT style
 * license that can be found in the LICENSE file.
 */

namespace Focela\Laratrust\Hashing;

use Closure;

class CallbackHasher implements HasherInterface
{
    /**
     * The closure used for hashing a value.
     *
     * @var \Closure
     */
    protected $hash;

    /**
     * The closure used for checking a hashed value.
     *
     * @var \Closure
     */
    protected $check;

    /**
     * Constructor.
     *
     * @param \Closure $hash
     * @param \Closure $check
     *
     * @return void
     */
    public function __construct(\Closure $hash, \Closure $check)
    {
        $this->hash = $hash;

        $this->check = $check;
    }

    /**
     * @inheritdoc
     */
    public function hash(string $value): string
    {
        $callback = $this->hash;

        return $callback($value);
    }

    /**
     * @inheritdoc
     */
    public function check(string $value, string $hashedValue): bool
    {
        $callback = $this->check;

        return $callback($value, $hashedValue);
    }
}
