<?php
/*
 * Copyright (c) 2024 Focela Technologies. All rights reserved.
 * Use of this source code is governed by a MIT style
 * license that can be found in the LICENSE file.
 */

namespace Focela\Laratrust\Activations;

interface ActivationInterface
{
    /**
     * Returns the random string used for the activation code.
     *
     * @return string
     */
    public function getCode(): string;
}
