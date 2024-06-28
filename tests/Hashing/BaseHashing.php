<?php
/*
 * Copyright (c) 2024 Focela Technologies. All rights reserved.
 * Use of this source code is governed by a MIT style
 * license that can be found in the LICENSE file.
 */

namespace Focela\Laratrust\Tests\Hashing;

use PHPUnit\Framework\TestCase;
use Focela\Laratrust\Hashing\HasherInterface;

abstract class BaseHashing extends TestCase
{
    /**
     * The Hasher instance.
     *
     * @var HasherInterface
     */
    protected $hasher;

    /** @test */
    public function a_password_can_be_hashed()
    {
        $hashedValue = $this->hasher->hash('password');

        $this->assertTrue($hashedValue !== 'password');
        $this->assertTrue($this->hasher->check('password', $hashedValue));
        $this->assertFalse($this->hasher->check('fail', $hashedValue));
    }

    /** @test */
    public function a_password_that_is_not_very_long_in_length_can_be_hashed()
    {
        $hashedValue = $this->hasher->hash('foo');

        $this->assertTrue($hashedValue !== 'foo');
        $this->assertTrue($this->hasher->check('foo', $hashedValue));
        $this->assertFalse($this->hasher->check('fail', $hashedValue));
    }

    /** @test */
    public function a_password_with_utf8_characters_can_be_hashed()
    {
        $hashedValue = $this->hasher->hash('fÄÓñ');

        $this->assertTrue($hashedValue !== 'fÄÓñ');
        $this->assertTrue($this->hasher->check('fÄÓñ', $hashedValue));
    }

    /** @test */
    public function a_password_with_various_symbols_can_be_hashed()
    {
        $hashedValue = $this->hasher->hash('!"#$%^&*()-_,./:;<=>?@[]{}`~|');

        $this->assertTrue($hashedValue !== '!"#$%^&*()-_,./:;<=>?@[]{}`~|');
        $this->assertTrue($this->hasher->check('!"#$%^&*()-_,./:;<=>?@[]{}`~|', $hashedValue));
    }

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        if (! $this->hasher) {
            throw new \RuntimeException();
        }
    }
}
