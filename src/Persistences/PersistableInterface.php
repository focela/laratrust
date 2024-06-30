<?php
/*
 * Copyright (c) 2024 Focela Technologies. All rights reserved.
 * Use of this source code is governed by a MIT style
 * license that can be found in the LICENSE file.
 */

namespace Focela\Laratrust\Persistences;

interface PersistableInterface
{
    /**
     * Returns the persistable key value.
     *
     * @return string
     */
    public function getPersistableId(): string;

    /**
     * Returns the persistable key name.
     *
     * @return string
     */
    public function getPersistableKey(): string;

    public function setPersistableKey(string $key);

    /**
     * Returns the persistable relationship name.
     *
     * @return string
     */
    public function getPersistableRelationship(): string;

    public function setPersistableRelationship(string $persistableRelationship);

    /**
     * Generates a random persist code.
     *
     * @return string
     */
    public function generatePersistenceCode(): string;
}
