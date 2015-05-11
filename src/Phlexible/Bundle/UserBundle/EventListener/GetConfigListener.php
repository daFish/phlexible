<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\UserBundle\EventListener;

use Phlexible\Bundle\GuiBundle\Event\GetConfigEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;
use Symfony\Component\Security\Core\Role\SwitchUserRole;

/**
 * Get config listener
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class GetConfigListener
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var RoleHierarchyInterface
     */
    private $roleHierarchy;

    /**
     * @var array
     */
    private $defaults;

    /**
     * @var int
     */
    private $passwordMinLength;

    /**
     * @param TokenStorageInterface  $tokenStorage
     * @param RoleHierarchyInterface $roleHierarchy
     * @param array                  $defaults
     * @param int                    $passwordMinLength
     */
    public function __construct(
        TokenStorageInterface $tokenStorage,
        RoleHierarchyInterface $roleHierarchy,
        array $defaults,
        $passwordMinLength)
    {
        $this->tokenStorage = $tokenStorage;
        $this->roleHierarchy = $roleHierarchy;
        $this->defaults = $defaults;
        $this->passwordMinLength = $passwordMinLength;
    }

    /**
     * @param GetConfigEvent $event
     */
    public function onGetConfig(GetConfigEvent $event)
    {
        $token = $this->tokenStorage->getToken();
        $user = $token->getUser();

        $roles = [];
        foreach ($this->roleHierarchy->getReachableRoles($token->getRoles()) as $role) {
            $roles[] = $role->getRole();
        }

        $previousUsername = '';
        foreach ($this->tokenStorage->getToken()->getRoles() as $role) {
            if ($role instanceof SwitchUserRole) {
                $adminUser = $role->getSource()->getUser();
                $previousUsername = $adminUser->getUsername();
            }
        }

        $event->getConfig()
            ->set('system.password_min_length', $this->passwordMinLength)
            ->set('user.id', $user->getId())
            ->set('user.username', $user->getUsername())
            ->set('user.email', $user->getEmail())
            ->set('user.firstname', $user->getFirstname() ?: '')
            ->set('user.lastname', $user->getLastname() ?: '')
            ->set('user.displayName', $user->getDisplayName() ?: '')
            ->set('user.properties', $user->getProperties())
            ->set('user.roles', $roles)
            ->set('user.previousUsername', $previousUsername)
            ->set('defaults', $this->defaults);

        foreach ($user->getProperties() as $key => $value) {
            $event->getConfig()->set('user.property.' . $key, $value);
        }
    }
}
