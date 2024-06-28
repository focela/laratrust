<?php
/*
 * Copyright (c) 2024 Focela Technologies. All rights reserved.
 * Use of this source code is governed by a MIT style
 * license that can be found in the LICENSE file.
 */

namespace Focela\Laratrust\Cookies;

class NullCookie implements CookieInterface
{
    /**
     * Put a value in the Laratrust cookie (to be stored until it's cleared).
     *
     * @param mixed $value
     *
     * @return void
     */
    public function put($value): void
    {
    }

    /**
     * Returns the Laratrust cookie value.
     *
     * @return mixed
     */
    public function get()
    {
        return null;
    }

    /**
     * Remove the Laratrust cookie.
     *
     * @return void
     */
    public function forget(): void
    {
    }
}
