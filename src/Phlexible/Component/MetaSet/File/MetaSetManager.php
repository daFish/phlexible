<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\MetaSet\File;

use Phlexible\Component\MetaSet\Model\MetaSet;
use Phlexible\Component\MetaSet\Model\MetaSetCollection;
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
     * @var MetaSetCollection
     */
    private $metaSets;

    /**
     * @param MetaSetRepositoryInterface $repository
     */
    public function __construct(MetaSetRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @return MetaSetCollection
     */
    public function getCollection()
    {
        if ($this->metaSets === null) {
            $this->metaSets = $this->repository->loadMetaSets();
        }

        return $this->metaSets;
    }

    /**
     * {@inheritdoc}
     */
    public function find($id)
    {
        return $this->getCollection()->get($id);
    }

    /**
     * @param string $name
     *
     * @return MetaSet
     */
    public function findOneByName($name)
    {
        return $this->getCollection()->getByName($name);
    }

    /**
     * {@inheritdoc}
     */
    public function findAll()
    {
        return $this->getCollection()->all();
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
        $this->repository->dumpMetaSet($metaSet, 'xml');
    }
}
