<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Phlexible\Bundle\TreeBundle\Entity\NodeLock;
use Phlexible\Bundle\TreeBundle\Node\NodeContext;

/**
 * Node lock repository
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
