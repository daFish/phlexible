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

use Phlexible\Component\AccessControl\Exception\InvalidArgumentException;

/**
 * Permission resolver
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class PermissionResolver
{
    /**
     * @var PermissionCollection
     */
    private $permissions;

    /**
     * @param PermissionCollection $permissions
     */
    public function __construct(PermissionCollection $permissions)
    {
        $this->permissions = $permissions;
    }

    /**
     * @param string $contentClass
     * @param int    $mask
     *
     * @throws InvalidArgumentException
     * @return Permission[]
     */
    public function resolve($contentClass, $mask)
    {
        $permissions = [];

        foreach ($this->permissions->getByContentClass($contentClass) as $permission) {
            if ($permission->getBit() & $mask) {
                $permissions[] = $permission;
                $mask = $mask ^ $permission->getBit();
            }
        }

        if ($mask) {
            $bits = decbin($mask);
            throw new InvalidArgumentException("Permission for bits $bits not found.");
        }

        return $permissions;
    }
}
