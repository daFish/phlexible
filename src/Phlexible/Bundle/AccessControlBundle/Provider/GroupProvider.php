<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\AccessControlBundle\Provider;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Phlexible\Component\AccessControl\Provider\ProviderInterface;

/**
 * Group provider
 *
 * @author Marco Fischer <mf@brainbits.net>
 */
class GroupProvider implements ProviderInterface
{
    /**
     * @var EntityRepository
     */
    private $groupRepository;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->groupRepository = $entityManager->getRepository('PhlexibleUserBundle:Group');
    }

    /**
     * Return object name
     *
     * @param string $objectType
     * @param string $objectId
     *
     * @return string
     */
    public function getName($objectType, $objectId)
    {
        return $this->groupRepository->find($objectId)->getName();
    }

    /**
     * Return objects
     *
     * @param string $query
     * @param int    $limit
     * @param int    $offset
     *
     * @return array
     */
    public function getAll($query, $limit, $offset)
    {
        $groups = $this->groupRepository->findAll();

        $data = [];
        foreach ($groups as $group) {
            $data[] = [
                'type'       => 'group',
                'objectType' => 'gid',
                'objectId'   => $group->getId(),
                'label'      => $group->getName(),
            ];
        }

        return [
            'count' => count($data),
            'data'  => $data,
        ];
    }
}
