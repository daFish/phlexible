<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\TreeBundle\Model;

use Phlexible\Bundle\TreeBundle\Exception\NodeNotFoundException;
use Phlexible\Component\Tree\TreeContextInterface;

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
     * @param TreeContextInterface $treeContext
     * @param string               $siterootId
     *
     * @return TreeInterface
     */
    public function getBySiteRootId(TreeContextInterface $treeContext, $siterootId);

    /**
     * Get tree by node ID
     *
     * @param TreeContextInterface $treeContext
     * @param int                  $nodeId
     *
     * @return TreeInterface
     * @throws NodeNotFoundException
     */
    public function getByNodeId(TreeContextInterface $treeContext, $nodeId);

    /**
     * @param TreeContextInterface $treeContext
     *
     * @return TreeInterface[]
     */
    public function getAll(TreeContextInterface $treeContext);

    /**
     * @param TreeContextInterface $treeContext
     * @param string               $siterootId
     * @param string               $type
     * @param string               $typeId
     * @param string               $userId
     *
     * @return TreeInterface
     */
    public function createTree(TreeContextInterface $treeContext, $siterootId, $type, $typeId, $userId);
}
