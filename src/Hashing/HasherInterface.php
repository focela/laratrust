<?php
/*
 * Copyright (c) 2024 Focela Technologies. All rights reserved.
 * Use of this source code is governed by a MIT style
 * license that can be found in the LICENSE file.
 */

namespace Focela\Laratrust\Hashing;

interface HasherInterface
{
    /**
     * Hash the given value.
     *
     * @param string $value
     *
     * @throws \RuntimeException
     *
     * @return string
     */
    public function hash(string $value): string;

    /**
     * Checks the string against the hashed value.
     *
     * @param string $value
     * @param string $hashedValue
     *
     * @return bool
     */
    public function check(string $value, string $hashedValue): bool;
}
