<?php
/*
 * Copyright (c) 2024 Focela Technologies. All rights reserved.
 * Use of this source code is governed by a MIT style
 * license that can be found in the LICENSE file.
 */

namespace Focela\Laratrust\Hashing;

trait Hasher
{
    /**
     * The salt length.
     *
     * @var int
     */
    protected $saltLength = 22;

    /**
     * Create a random string for a salt.
     *
     * @return string
     */
    protected function createSalt(): string
    {
        $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ./';

        $max = strlen($pool) - 1;

        $salt = '';

        for ($i = 0; $i < $this->saltLength; $i++) {
            $salt .= $pool[random_int(0, $max)];
        }

        return $salt;
    }

    /**
     * Compares two strings $a and $b in length-constant time.
     *
     * @param string $a
     * @param string $b
     *
     * @return bool
     */
    protected function slowEquals(string $a, string $b): bool
    {
        $diff = strlen($a) ^ strlen($b);

        for ($i = 0; $i < strlen($a) && $i < strlen($b); $i++) {
            $diff |= ord($a[$i]) ^ ord($b[$i]);
        }

        return $diff === 0;
    }
}
