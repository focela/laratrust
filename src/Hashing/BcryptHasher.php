<?php
/*
 * Copyright (c) 2024 Focela Technologies. All rights reserved.
 * Use of this source code is governed by a MIT style
 * license that can be found in the LICENSE file.
 */

namespace Focela\Laratrust\Hashing;

class BcryptHasher implements HasherInterface
{
    use Hasher;

    /**
     * The hash strength.
     *
     * @var int
     */
    public $strength = 8;

    /**
     * @inheritdoc
     */
    public function hash(string $value): string
    {
        $salt = $this->createSalt();

        $strength = str_pad($this->strength, 2, '0', STR_PAD_LEFT);

        $prefix = '$2y$';

        return crypt($value, $prefix.$strength.'$'.$salt.'$');
    }

    /**
     * @inheritdoc
     */
    public function check(string $value, string $hashedValue): bool
    {
        return $this->slowEquals(crypt($value, $hashedValue), $hashedValue);
    }
}
