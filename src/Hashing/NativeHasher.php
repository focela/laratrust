<?php
/*
 * Copyright (c) 2024 Focela Technologies. All rights reserved.
 * Use of this source code is governed by a MIT style
 * license that can be found in the LICENSE file.
 */

namespace Focela\Laratrust\Hashing;

class NativeHasher implements HasherInterface
{
    /**
     * @inheritdoc
     */
    public function hash(string $value): string
    {
        return password_hash($value, PASSWORD_DEFAULT);
    }

    /**
     * @inheritdoc
     */
    public function check(string $value, string $hashedValue): bool
    {
        return password_verify($value, $hashedValue);
    }
}
