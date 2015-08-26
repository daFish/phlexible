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

/**
 * Element version repository
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ElementVersionRepository extends EntityRepository
{
    /**
     * @param Element $element
     *
     * @return array
     */
    public function getVersions(Element $element, $dir = 'asc')
    {
        $qb = $this->createQueryBuilder('ev');

        $qb
            ->select(array('ev.id', 'ev.version', 'ev.createdAt', 'ev.format'))
            ->where($qb->expr()->eq('ev.element', $element->getEid()))
            ->orderBy('ev.version', strtolower($dir) === 'desc' ? 'ASC' : 'DESC');

        return $qb->getQuery()->getScalarResult();
    }
}
