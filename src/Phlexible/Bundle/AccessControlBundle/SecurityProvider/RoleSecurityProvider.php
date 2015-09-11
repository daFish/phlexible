<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\AccessControlBundle\SecurityProvider;

use Phlexible\Component\AccessControl\SecurityProvider\SecurityProviderInterface;
use Phlexible\Component\AccessControl\SecurityProvider\SecurityResolverInterface;
use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;

/**
 * Role security provider
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class RoleSecurityProvider implements SecurityProviderInterface, SecurityResolverInterface
{
    /**
     * @var RoleHierarchyInterface
     */
    private $roleHierarchy;

    /**
     * @var array
     */
    private $roles;

    /**
     * @param RoleHierarchyInterface $roleHierarchy
     * @param array                  $roles
     */
    public function __construct(RoleHierarchyInterface $roleHierarchy, array $roles)
    {
        $this->roleHierarchy = $roleHierarchy;
        $this->roles = $roles;
    }

    /**
     * {@inheritdoc}
     */
    public function resolveName($securityType, $securityId)
    {
        if ($securityType !== 'Symfony\Component\Security\Core\Role') {
            return null;
        }

        return $securityId;
    }

    /**
     * {@inheritdoc}
     */
    public function getAll($query, $limit, $offset)
    {
        $data = array();
        foreach ($this->roles as $role) {
            $data[] = array(
                'securityType' => 'Symfony\Component\Security\Core\Role',
                'securityId'   => $role,
                'securityName' => $role,
            );
        }

        return array(
            'count' => count($data),
            'data'  => $data,
        );
    }
}
