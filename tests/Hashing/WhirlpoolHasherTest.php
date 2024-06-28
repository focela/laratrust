<?php
/*
 * Copyright (c) 2024 Focela Technologies. All rights reserved.
 * Use of this source code is governed by a MIT style
 * license that can be found in the LICENSE file.
 */

namespace Focela\Laratrust\Tests\Hashing;

use Focela\Laratrust\Hashing\WhirlpoolHasher;

class WhirlpoolHasherTest extends BaseHashing
{
    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $this->hasher = new WhirlpoolHasher();

        parent::setUp();
    }
}
