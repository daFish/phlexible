<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\AccessControl\Permission;

/**
 * Permission provider interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface PermissionProviderInterface
{
    /**
     * Return permissions
     *
     * @return PermissionCollection
     */
    public function getPermissions();
}
