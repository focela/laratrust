<?php
/*
 * Copyright (c) 2024 Focela Technologies. All rights reserved.
 * Use of this source code is governed by a MIT style
 * license that can be found in the LICENSE file.
 */

namespace Focela\Laratrust\Laravel\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @mixin \Focela\Laratrust\Laratrust
 */
class Laratrust extends Facade
{
    /**
     * @inheritdoc
     */
    protected static function getFacadeAccessor()
    {
        return 'laratrust';
    }
}
