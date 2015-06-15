<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Phlexible\Bundle\ElementBundle\Entity\Element;
use Phlexible\Bundle\ElementBundle\Entity\ElementLock;

/**
 * Element lock repository
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ElementLockRepository extends EntityRepository
{
    /**
     * @param Element $element
     * @param string  $userId
     *
     * @return ElementLock
     */
    public function findByElementAndUserId(Element $element, $userId)
    {
        return $this->findBy(['element' => $element, 'userId' => $userId]);
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
        return $this->findBy(['element' => $element]);
    }
}
