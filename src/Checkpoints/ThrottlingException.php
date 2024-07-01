<?php
/*
 * Copyright (c) 2024 Focela Technologies. All rights reserved.
 * Use of this source code is governed by a MIT style
 * license that can be found in the LICENSE file.
 */

namespace Focela\Laratrust\Checkpoints;

use Carbon\Carbon;

class ThrottlingException extends \RuntimeException
{
    /**
     * The delay, in seconds.
     *
     * @var int
     */
    protected $delay = 0;

    /**
     * The throttling type which caused the exception.
     *
     * @var string
     */
    protected $type = '';

    /**
     * Returns the delay.
     *
     * @return int
     */
    public function getDelay(): int
    {
        return $this->delay;
    }

    /**
     * Sets the delay.
     *
     * @param int $delay
     *
     * @return $this
     */
    public function setDelay(int $delay): self
    {
        $this->delay = $delay;

        return $this;
    }

    /**
     * Returns the type.
     *
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Sets the type.
     *
     * @param string $type
     *
     * @return $this
     */
    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Returns a Carbon object representing the time which the throttle is lifted.
     *
     * @return Carbon
     */
    public function getFree(): Carbon
    {
        return Carbon::now()->addSeconds($this->delay);
    }
}
