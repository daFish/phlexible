<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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
        return array(
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
        );
    }

    /**
     * {@inheritdoc}
     */
    public function exposeRoles()
    {
        return $this->provideRoles();
    }
}
