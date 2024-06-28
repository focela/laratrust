<?php
/*
 * Copyright (c) 2024 Focela Technologies. All rights reserved.
 * Use of this source code is governed by a MIT style
 * license that can be found in the LICENSE file.
 */

namespace Focela\Laratrust\Hashing;

class Sha256Hasher implements HasherInterface
{
    use Hasher;

    /**
     * @inheritdoc
     */
    public function hash(string $value): string
    {
        $salt = $this->createSalt();

        return $salt.hash('sha256', $salt.$value);
    }

    /**
     * @inheritdoc
     */
    public function check(string $value, string $hashedValue): bool
    {
        $salt = substr($hashedValue, 0, $this->saltLength);

        return $this->slowEquals($salt.hash('sha256', $salt.$value), $hashedValue);
    }
}
