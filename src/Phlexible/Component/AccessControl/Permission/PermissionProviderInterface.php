<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
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
