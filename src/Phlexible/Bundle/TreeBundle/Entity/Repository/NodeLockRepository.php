<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Phlexible\Bundle\ElementBundle\Entity\Element;
use Phlexible\Bundle\TreeBundle\Entity\NodeLock;

/**
 * Node lock repository
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class NodeLockRepository extends EntityRepository
{
    /**
     * @param Element $element
     * @param string  $userId
     *
     * @return NodeLock
     */
    public function findByNodeAndUserId(Element $element, $userId)
    {
        return $this->findBy(array('element' => $element, 'userId' => $userId));
    }

    /**
     * @param Element $element
     * @param string  $notUserId
     *
     * @return ElementLock
     */
    public function findByElementAndNotUserId(Element $element, $notUserId)
    {
        $qb = $this->createQueryBuilder('l');
        $qb
            ->where($qb->expr()->eq('l.element', $element->getEid()))
            ->andWhere($qb->expr()->neq('l.userId', $qb->expr()->literal($notUserId)));

        return $qb->getQuery()->getResult();
    }

    /**
     * @param Element $element
     *
     * @return ElementLock[]
     */
    public function findByEid(Element $element)
    {
        return $this->findBy(array('element' => $element));
    }
}
