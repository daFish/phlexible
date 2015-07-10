<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\Model;

use Phlexible\Bundle\TreeBundle\Exception\NodeNotFoundException;

/**
 * Tree manager interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface TreeManagerInterface
{
    /**
     * Return tree by siteroot ID
     *
     * @param string $siteRootId
     *
     * @return TreeInterface
     */
    public function getBySiteRootId($siteRootId);

    /**
     * Get tree by node ID
     *
     * @param int $nodeId
     *
     * @return TreeInterface
     * @throws NodeNotFoundException
     */
    public function getByNodeId($nodeId);

    /**
     * Get tree by type ID
     *
     * @param int    $typeId
     * @param string $type
     *
     * @return TreeInterface[]
     * @throws NodeNotFoundException
     */
    public function getByTypeId($typeId, $type = null);

    /**
     * @return TreeInterface[]
     */
    public function getAll();

    /**
     * @param string $siterootId
     * @param string $type
     * @param string $typeId
     * @param string $userId
     *
     * @return TreeInterface
     */
    public function createTree($siterootId, $type, $typeId, $userId);
}
