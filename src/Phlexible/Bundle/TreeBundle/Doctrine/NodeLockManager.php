<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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
    public function isLocked(NodeContext $node)
    {
        $lock = $this->getLockRepository()->findOneBy(array('nodeId' => $node->getId()));

        return $lock !== null;
    }

    /**
     * {@inheritdoc}
     */
    public function isLockedByUser(NodeContext $node, $userId)
    {
        $lock = $this->getLockRepository()->findOneBy(array('nodeId' => $node->getId(), 'userId' => $userId));

        return $lock !== null;
    }

    /**
     * {@inheritdoc}
     */
    public function isLockedByOtherUser(NodeContext $node, $userId)
    {
        return $this->getLockRepository()->lockExistsByNodeAndNotUserId($node, $userId);
    }

    /**
     * {@inheritdoc}
     */
    public function lock(NodeContext $node, $userId, $type = NodeLock::TYPE_TEMPORARY)
    {
        if ($this->isLockedByOtherUser($node, $userId)) {
            throw new LockFailedException('Can\'t aquire lock, already locked.');
        }

        $lock = new NodeLock(
            $node->getId(),
            $userId,
            $type
        );

        $this->entityManager->persist($lock);
        $this->entityManager->flush($lock);

        return $lock;
    }

    /**
     * {@inheritdoc}
     */
    public function unlock(NodeContext $node)
    {
        if ($this->isLocked($node)) {
            throw new LockFailedException('Can\'t aquire lock, already locked.');
        }

        $lock = $this->getLockRepository()->findOneBy(array('nodeId' => $node->getId()));

        $this->entityManager->remove($lock);
    }

    /**
     * {@inheritdoc}
     */
    public function findLock(NodeContext $node)
    {
        if (!$this->isLocked($node)) {
            return null;
        }

        return $this->getLockRepository()->findOneBy(array('nodeId' => $node->getId()));
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
