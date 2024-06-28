<?php
/*
 * Copyright (c) 2024 Focela Technologies. All rights reserved.
 * Use of this source code is governed by a MIT style
 * license that can be found in the LICENSE file.
 */

namespace Focela\Laratrust\Tests\Hashing;

use Focela\Laratrust\Hashing\CallbackHasher;

class CallbackHasherTest extends BaseHashing
{
    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        // Never use this hashing strategy!
        $hash = function ($value) {
            return strrev($value);
        };

        $check = function ($value, $hashedValue) {
            return strrev($value) === $hashedValue;
        };

        $this->hasher = new CallbackHasher($hash, $check);

        parent::setUp();
    }
}
