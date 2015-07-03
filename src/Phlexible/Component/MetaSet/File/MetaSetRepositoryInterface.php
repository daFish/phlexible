<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\MetaSet\File;

use Phlexible\Component\MetaSet\Model\MetaSetCollection;
use Phlexible\Component\MetaSet\Model\MetaSetInterface;

/**
 * Meta set repository interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface MetaSetRepositoryInterface
{
    /**
     * @return MetaSetCollection
     */
    public function loadAll();

    /**
     * @param string $id
     *
     * @return MetaSetInterface
     */
    public function load($id);

    /**
     * @param string $name
     *
     * @return MetaSetInterface
     */
    public function loadByName($name);

    /**
     * @param MetaSetInterface $metaSet
     * @param string           $type
     */
    public function writeMetaSet(MetaSetInterface $metaSet, $type = null);
}
