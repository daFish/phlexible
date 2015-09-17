<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\TreeBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Phlexible\Bundle\TreeBundle\Entity\NodeLock;
use Phlexible\Bundle\TreeBundle\Node\NodeContext;

/**
 * Node lock repository.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class NodeLockRepository extends EntityRepository
{
    /**
     * @param NodeContext $node
     * @param string      $notUserId
     *
     * @return NodeLock
     */
    public function lockExistsByNodeAndNotUserId(NodeContext $node, $notUserId)
    {
        $qb = $this->createQueryBuilder('l');
        $qb
            ->select('l.id')
            ->where($qb->expr()->eq('l.nodeId', $node->getId()))
            ->andWhere($qb->expr()->neq('l.userId', $qb->expr()->literal($notUserId)))
            ->setMaxResults(1);

        return count($qb->getQuery()->getScalarResult()) > 0;
    }
}
