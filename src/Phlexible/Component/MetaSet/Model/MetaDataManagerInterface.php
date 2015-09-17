<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\MetaSet\Model;

use Phlexible\Component\MetaSet\Domain\MetaSet;

/**
 * Meta data manager interface.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface MetaDataManagerInterface
{
    /**
     * Load meta data.
     *
     * @param MetaSet $metaSet
     * @param array   $identifiers
     *
     * @return MetaDataInterface
     */
    public function findByMetaSetAndIdentifiers(MetaSet $metaSet, array $identifiers);

    /**
     * @param MetaSet $metaSet
     *
     * @return MetaDataInterface
     */
    public function findByMetaSet(MetaSet $metaSet);

    /**
     * @return MetaDataInterface[]
     */
    public function findAll();

    /**
     * @param MetaSet $metaSet
     *
     * @return MetaDataInterface
     */
    public function createMetaData(MetaSet $metaSet);

    /**
     * @param MetaDataInterface $metaData
     */
    public function updateMetaData(MetaDataInterface $metaData);
}
