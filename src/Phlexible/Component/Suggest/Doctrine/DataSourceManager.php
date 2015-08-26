<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\Suggest\Doctrine;

use Doctrine\ORM\EntityManager;
use Phlexible\Bundle\DataSourceBundle\DataSource\DataSourceRepository;
use Phlexible\Bundle\DataSourceBundle\Entity\DataSource;
use Phlexible\Component\Suggest\Model\DataSourceManagerInterface;

/**
 * Doctrine data source manager
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class DataSourceManager implements DataSourceManagerInterface
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var DataSourceRepository
     */
    private $dataSourceRepository;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;

        $this->dataSourceRepository = $entityManager->getRepository('PhlexibleDataSourceBundle:DataSource');
    }

    /**
     * {@inheritdoc}
     */
    public function find($id)
    {
        return $this->dataSourceRepository->find($id);
    }

    /**
     * {@inheritdoc}
     */
    public function findBy(array $criteria, $orderBy = null, $limit = null, $offset = null)
    {
        return $this->dataSourceRepository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * {@inheritdoc}
     */
    public function updateDataSource(DataSource $dataSource)
    {
        $this->entityManager->persist($dataSource);
        foreach ($dataSource->getValueBags() as $value) {
            $this->entityManager->persist($value);
        }
        $this->entityManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function deleteDataSource(DataSource $dataSource)
    {
        $this->entityManager->remove($dataSource);
        $this->entityManager->flush();
    }
}
