<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
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
