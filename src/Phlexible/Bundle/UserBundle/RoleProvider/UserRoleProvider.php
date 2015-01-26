<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\UserBundle\RoleProvider;

use Phlexible\Bundle\GuiBundle\Security\RoleProvider\RoleProvider;

/**
 * User role provider
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class UserRoleProvider extends RoleProvider
{
    /**
     * {@inheritdoc}
     */
    public function provideRoles()
    {
        return [
            'ROLE_USERS',
            'ROLE_SWITCH_USER',
            'ROLE_GROUPS',
            'ROLE_USER_ADMIN_READ',
            'ROLE_USER_ADMIN_CREATE',
            'ROLE_USER_ADMIN_UPDATE',
            'ROLE_USER_ADMIN_DELETE',
            'ROLE_GROUP_ADMIN_READ',
            'ROLE_GROUP_ADMIN_CREATE',
            'ROLE_GROUP_ADMIN_UPDATE',
            'ROLE_GROUP_ADMIN_DELETE',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function exposeRoles()
    {
        return $this->provideRoles();
    }
}
