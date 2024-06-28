<?php
/*
 * Copyright (c) 2024 Focela Technologies. All rights reserved.
 * Use of this source code is governed by a MIT style
 * license that can be found in the LICENSE file.
 */

namespace Focela\Laratrust\Tests\Hashing;

use Focela\Laratrust\Hashing\Sha256Hasher;

class Sha256HasherTest extends BaseHashing
{
    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $this->hasher = new Sha256Hasher();

        parent::setUp();
    }
}
