<?php
/*
 * Copyright (c) 2024 Focela Technologies. All rights reserved.
 * Use of this source code is governed by a MIT style
 * license that can be found in the LICENSE file.
 */

namespace Focela\Laratrust\Sessions;

interface SessionInterface
{
    /**
     * Put a value in the Laratrust session.
     *
     * @param mixed $value
     *
     * @return void
     */
    public function put($value): void;

    /**
     * Returns the Laratrust session value.
     *
     * @return mixed
     */
    public function get();

    /**
     * Removes the Laratrust session.
     *
     * @return void
     */
    public function forget(): void;
}
