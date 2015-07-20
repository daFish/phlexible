<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\Doctrine;

use Doctrine\ORM\EntityManager;
use Phlexible\Bundle\TreeBundle\Entity\NodeLock;
use Phlexible\Bundle\TreeBundle\Entity\Repository\NodeLockRepository;
use Phlexible\Bundle\TreeBundle\Exception\LockFailedException;
use Phlexible\Bundle\TreeBundle\Model\NodeLockManagerInterface;
use Phlexible\Bundle\TreeBundle\Node\NodeContext;

/**
 * Node lock manager
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @author Phillip Look <pl@brainbits.net>
 */
class NodeLockManager implements NodeLockManagerInterface
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var NodeLockRepository
     */
    private $lockRepository;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @return NodeLockRepository
     */
    public function getLockRepository()
    {
        if (null === $this->lockRepository) {
            $this->lockRepository = $this->entityManager->getRepository('PhlexibleTreeBundle:NodeLock');
        }

        return $this->lockRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function isLocked(NodeContext $node, $language)
    {
        if ($this->isMasterLocked($node)) {
            return true;
        }

        if ($element->getMasterLanguage() === $language) {
            return false;
        }

        return $this->isSlaveLocked($element, $language);
    }

    /**
     * {@inheritdoc}
     */
    public function isMasterLocked(NodeContext $node)
    {
        $locks = $this->getLockRepository()->findBy(array('nodeId' => $node->getId()));

        foreach ($locks as $lock) {
            if ($lock->getLanguage() === null) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function isSlaveLocked(NodeContext $node, $language)
    {
        $locks = $this->getLockRepository()->findBy(array('nodeId' => $node->getId()));

        foreach ($locks as $lock) {
            if ($lock->getLanguage() === $language) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function isLockedByUser(NodeContext $node, $language, $userId)
    {
        if ($this->isMasterLockedByUser($node, $userId)) {
            return true;
        }

        if ($element->getMasterLanguage() === $language) {
            return false;
        }

        return $this->isSlaveLockedByUser($element, $language, $userId);
    }

    /**
     * {@inheritdoc}
     */
    public function isMasterLockedByUser(NodeContext $node, $userId)
    {
        $locks = $this->getLockRepository()->findBy(array('nodeId' => $node->getId(), 'userId' => $userId));

        foreach ($locks as $lock) {
            if ($lock->getLanguage() === null) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function isSlaveLockedByUser(NodeContext $node, $language, $userId)
    {
        $locks = $this->getLockRepository()->findBy(array('nodeId' => $node->getId(), 'userId' => $userId));

        foreach ($locks as $lock) {
            if ($lock->getLanguage() === $language) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function isLockedByOtherUser(NodeContext $node, $language, $userId)
    {
        if ($this->isMasterLockedByOtherUser($node, $userId)) {
            return true;
        }

        if ($element->getMasterLanguage() === $language) {
            return false;
        }

        return $this->isSlaveLockedByOtherUser($node, $language, $userId);
    }

    /**
     * {@inheritdoc}
     */
    public function isMasterLockedByOtherUser(NodeContext $node, $userId)
    {
        $locks = $this->getLockRepository()->findByNodeAndNotUserId($node, $userId);

        foreach ($locks as $lock) {
            if ($lock->getLanguage() === null) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function isSlaveLockedByOtherUser(NodeContext $node, $language, $userId)
    {
        $locks = $this->getLockRepository()->findByNodeAndNotUserId($node, $userId);

        foreach ($locks as $lock) {
            if ($lock->getLanguage() === $language) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function lock(NodeContext $node, $userId, $language = null, $type = NodeLock::TYPE_TEMPORARY)
    {
        if (!$language || $element->getMasterLanguage() === $language) {
            if ($this->isMasterLockedByOtherUser($element, $userId)) {
                throw new LockFailedException('Can\'t aquire lock, already locked.');
            }
        } else {
            if ($this->isSlaveLockedByOtherUser($element, $language, $userId)) {
                throw new LockFailedException('Can\'t aquire lock, already locked.');
            }
        }

        $lock = new NodeLock(
            $node->getId(),
            $language,
            $type,
            $userId
        );

        $this->entityManager->persist($lock);
        $this->entityManager->flush($lock);

        return $lock;
    }

    /**
     * {@inheritdoc}
     */
    public function unlock(NodeContext $node, $language = null)
    {
        if (!$language || $element->getMasterLanguage() === $language) {
            if ($this->isMasterLocked($node)) {
                throw new LockFailedException('Can\'t aquire lock, already locked.');
            }
        } else {
            if ($this->isSlaveLocked($node, $language)) {
                throw new LockFailedException('Can\'t aquire lock, already locked.');
            }
        }

        $lock = $this->getLockRepository()->findOneBy(array('nodeId' => $node->getId(), 'language' => $language));

        $this->entityManager->remove($lock);
    }

    /**
     * {@inheritdoc}
     */
    public function find($id)
    {
        return $this->getLockRepository()->find($id);
    }

    /**
     * {@inheritdoc}
     */
    public function findMasterLock(NodeContext $node)
    {
        return $this->getLockRepository()->findOneBy(array('nodeId' => $node->getId(), 'language' => null));
    }

    /**
     * {@inheritdoc}
     */
    public function findSlaveLock(NodeContext $node, $language)
    {
        return $this->getLockRepository()->findOneBy(array('nodeId' => $node->getId(), 'language' => $language));
    }

    /**
     * {@inheritdoc}
     */
    public function findAll()
    {
        return $this->getLockRepository()->findAll();
    }

    /**
     * {@inheritdoc}
     */
    public function findBy(array $criteria, array $sort = array(), $limit = null, $offset = null)
    {
        return $this->getLockRepository()->findBy($criteria, $sort, $limit, $offset);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteLock(NodeLock $lock)
    {
        $this->entityManager->remove($lock);
        $this->entityManager->flush();
    }
}
