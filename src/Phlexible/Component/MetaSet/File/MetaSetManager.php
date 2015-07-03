<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\MetaSet\File;

use Phlexible\Component\MetaSet\Model\MetaSet;
use Phlexible\Component\MetaSet\Model\MetaSetField;
use Phlexible\Component\MetaSet\Model\MetaSetInterface;
use Phlexible\Component\MetaSet\Model\MetaSetManagerInterface;

/**
 * File meta set manager
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class MetaSetManager implements MetaSetManagerInterface
{
    /**
     * @var MetaSetRepositoryInterface
     */
    private $repository;

    /**
     * @param MetaSetRepositoryInterface $repository
     */
    public function __construct(MetaSetRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * {@inheritdoc}
     */
    public function find($id)
    {
        return $this->repository->load($id);
    }

    /**
     * @param string $name
     *
     * @return MetaSet
     */
    public function findOneByName($name)
    {
        return $this->repository->loadByName($name);
    }

    /**
     * {@inheritdoc}
     */
    public function findAll()
    {
        return $this->repository->loadAll()->all();
    }

    /**
     * {@inheritdoc}
     */
    public function createMetaSet()
    {
        return new MetaSet();
    }

    /**
     * {@inheritdoc}
     */
    public function createMetaSetField()
    {
        return new MetaSetField();
    }

    /**
     * {@inheritdoc}
     */
    public function updateMetaSet(MetaSetInterface $metaSet)
    {
        $this->repository->writeMetaSet($metaSet, 'xml');
    }
}
