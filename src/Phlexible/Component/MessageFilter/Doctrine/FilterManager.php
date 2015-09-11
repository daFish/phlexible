<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\MessageFilter\Doctrine;

use Doctrine\ORM\EntityManager;
use Phlexible\Component\MessageFilter\Doctrine\Repository\FilterRepository;
use Phlexible\Component\MessageFilter\Domain\Filter;
use Phlexible\Component\MessageFilter\Model\FilterManagerInterface;

/**
 * Doctrine filter manager
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class FilterManager implements FilterManagerInterface
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var FilterRepository
     */
    private $filterRepository;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;

        $this->filterRepository = $entityManager->getRepository('Phlexible\Component\MessageFilter\Domain\Filter');
    }

    /**
     * {@inheritdoc}
     */
    public function create()
    {
        return new Filter();
    }

    /**
     * {@inheritdoc}
     */
    public function find($id)
    {
        return $this->filterRepository->find($id);
    }

    /**
     * {@inheritdoc}
     */
    public function findBy(array $criteria, $orderBy = null, $limit = null, $offset = null)
    {
        return $this->filterRepository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * {@inheritdoc}
     */
    public function findOneBy(array $criteria, $orderBy = null)
    {
        return $this->filterRepository->findOneBy($criteria, $orderBy);
    }

    /**
     * {@inheritdoc}
     */
    public function updateFilter(Filter $filter)
    {
        $this->entityManager->persist($filter);
        $this->entityManager->flush($filter);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteFilter(Filter $filter)
    {
        $this->entityManager->remove($filter);
        $this->entityManager->flush();
    }
}
