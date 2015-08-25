<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\AccessControl\Domain;

use FOS\UserBundle\Model\UserInterface;
use Phlexible\Component\AccessControl\Model\ObjectIdentityInterface;
use Phlexible\Component\AccessControl\Model\SecurityIdentityInterface;
use Phlexible\Component\AccessControl\Permission\Permission;
use Phlexible\Component\AccessControl\Permission\PermissionCollection;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Access control list
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class AccessControlList implements \Countable
{
    /**
     * @var PermissionCollection
     */
    private $permissions;

    /**
     * @var ObjectIdentityInterface
     */
    private $objectIdentity;

    /**
     * @var Entry[]
     */
    private $entries;

    /**
     * @param PermissionCollection    $permissions
     * @param ObjectIdentityInterface $objectIdentity
     * @param Entry[]                 $accessControlEntries
     */
    public function __construct(
        PermissionCollection $permissions,
        ObjectIdentityInterface $objectIdentity,
        array $accessControlEntries = array()
    )
    {
        $this->permissions = $permissions;
        $this->objectIdentity = $objectIdentity;
        $this->entries = $accessControlEntries;
    }

    /**
     * @return PermissionCollection
     */
    public function getPermissions()
    {
        return $this->permissions;
    }

    /**
     * @return ObjectIdentityInterface
     */
    public function getObjectIdentity()
    {
        return $this->objectIdentity;
    }

    /**
     * @return Entry[]
     */
    public function getEntries()
    {
        return $this->entries;
    }

    /**
     * @param Entry $ace
     *
     * @return $this
     */
    public function addEntry(Entry $ace)
    {
        $this->entries[] = $ace;

        return $this;
    }

    /**
     * @param Entry $ace
     *
     * @return $this
     */
    public function removeEntry(Entry $ace)
    {
        foreach ($this->entries as $index => $entry) {
            if ($ace->getId() === $entry->getId()) {
                unset($this->entries[$index]);
            }
        }

        return $this;
    }

    /**
     * @param Permission     $permission
     * @param TokenInterface $token
     * @param string|null    $objectLanguage
     *
     * @return bool
     */
    public function check(Permission $permission, TokenInterface $token, $objectLanguage = null)
    {
        $user = $token->getUser();

        foreach ($this->entries as $entry) {
            if (
                $entry->getSecurityType() === 'Phlexible\Bundle\UserBundle\Entity\User' &&
                $entry->getSecurityIdentifier() === $user->getId()
            ) {
                return $permission->test($entry->getMask());
            }
            if ($entry->getSecurityType() === 'Phlexible\Bundle\UserBundle\Entity\Group') {
                foreach ($user->getGroups() as $group) {
                    if ($entry->getSecurityIdentifier() === $group->getId()) {
                        return $permission->test($entry->getMask());
                    }
                }
            }
        }

        return false;
    }

    /**
     * @param SecurityIdentityInterface $securityIdentity
     * @param int                       $mask
     * @param int                       $noInheritMask
     * @param int                       $stopMask
     * @param string|null               $contentLanguage
     *
     * @return $this
     */
    public function setAce(
        SecurityIdentityInterface $securityIdentity,
        $mask,
        $noInheritMask,
        $stopMask,
        $contentLanguage = null
    )
    {
        if ($contentLanguage === '_all_') {
            $contentLanguage = null;
        }

        $ace = null;
        foreach ($this->entries as $entry) {
            if ($entry->getSecurityIdentifier() === $securityIdentity->getIdentifier() && $entry->getSecurityType() == $securityIdentity->getType()) {
                $ace = $entry;
                break;
            }
        }

        if (!$ace) {
            $ace = new Entry();
            $ace
                ->setObjectType($this->objectIdentity->getType())
                ->setObjectId($this->objectIdentity->getIdentifier())
                ->setSecurityType($securityIdentity->getType())
                ->setSecurityId($securityIdentity->getIdentifier())
                ->setObjectLanguage($contentLanguage);
        }

        $ace
            ->setMask((int) $mask)
            ->setStopMask((int) $stopMask)
            ->setNoInheritMask((int) $noInheritMask);

        return $this;
    }

    /**
     * @param SecurityIdentityInterface $securityIdentity
     * @param string|null               $objectLanguage
     *
     * @return $this
     */
    public function removeAce(SecurityIdentityInterface $securityIdentity = null, $objectLanguage = null)
    {
        $aces = null;
        foreach ($this->entries as $index => $entry) {
            if ($entry->getSecurityIdentifier() === $securityIdentity->getIdentifier() && $entry->getSecurityType() == $securityIdentity->getType()) {
                unset($this->entries[$index]);
            }
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return count($this->entries);
    }
}
