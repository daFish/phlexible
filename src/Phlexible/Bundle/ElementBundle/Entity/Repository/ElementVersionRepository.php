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
    public function getVersions(Element $element)
    {
        $conn = $this->getEntityManager()->getConnection();

        $qb = $conn->createQueryBuilder();
        $qb
            ->select('ev.version')
            ->from('element_version', 'ev')
            ->where($qb->expr()->eq('ev.eid', $element->getEid()));

        $statement = $conn->executeQuery($qb->getSQL());

        $versions = [];
        while ($version = $statement->fetchColumn()) {
            $versions[] = (int) $version;
        }

        return $versions;
    }
}
