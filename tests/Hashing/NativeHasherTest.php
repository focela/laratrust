<?php
/*
 * Copyright (c) 2024 Focela Technologies. All rights reserved.
 * Use of this source code is governed by a MIT style
 * license that can be found in the LICENSE file.
 */

namespace Focela\Laratrust\Tests\Hashing;

use Focela\Laratrust\Hashing\NativeHasher;

class NativeHasherTest extends BaseHashing
{
    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $this->hasher = new NativeHasher();

        parent::setUp();
    }
}
