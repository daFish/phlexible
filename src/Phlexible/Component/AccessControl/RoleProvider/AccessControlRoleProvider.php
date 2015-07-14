<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\AccessControl\RoleProvider;

use Phlexible\Bundle\GuiBundle\Security\RoleProvider\RoleProvider;

/**
 * Access control role provider
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class AccessControlRoleProvider extends RoleProvider
{
    /**
     * {@inheritdoc}
     */
    public function provideRoles()
    {
        return array(
            'ROLE_ACCESS_CONTROL',
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
