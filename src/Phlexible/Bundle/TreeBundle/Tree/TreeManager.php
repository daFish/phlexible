<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\Tree;

use Phlexible\Bundle\SiterootBundle\Model\SiterootManagerInterface;
use Phlexible\Bundle\TreeBundle\Exception\NodeNotFoundException;
use Phlexible\Bundle\TreeBundle\Model\TreeFactoryInterface;
use Phlexible\Bundle\TreeBundle\Model\TreeInterface;
use Phlexible\Bundle\TreeBundle\Model\TreeNodeInterface;
use Phlexible\Bundle\TreeBundle\Model\WritableTreeInterface;

/**
 * Tree manager
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class TreeManager
{
    /**
     * @var TreeInterface[]
     */
    private $trees = array();

    /**
     * @var SiterootManagerInterface
     */
    private $siterootManager;

    /**
     * @var TreeFactoryInterface
     */
    private $treeFactory;

    /**
     * @param SiterootManagerInterface $siterootManager
     * @param TreeFactoryInterface     $treeFactory
     */
    public function __construct(SiterootManagerInterface $siterootManager, TreeFactoryInterface $treeFactory)
    {
        $this->siterootManager = $siterootManager;
        $this->treeFactory = $treeFactory;
    }

    /**
     * Return tree by siteroot ID
     *
     * @param string $siteRootId
     *
     * @return TreeInterface|WritableTreeInterface
     */
    public function getBySiteRootId($siteRootId)
    {
        if (!isset($this->trees[$siteRootId])) {
            $tree = $this->treeFactory->factory($siteRootId);
            $this->trees[$siteRootId] = $tree;
        }

        return $this->trees[$siteRootId];
    }

    /**
     * Get tree by node ID
     *
     * @param int $nodeId
     *
     * @return TreeInterface|WritableTreeInterface
     * @throws NodeNotFoundException
     */
    public function getByNodeId($nodeId)
    {
        foreach ($this->siterootManager->findAll() as $siteroot) {
            $tree = $this->getBySiteRootId($siteroot->getId());

            if ($tree->has($nodeId)) {
                return $tree;
            }
        }

        throw new NodeNotFoundException("Tree for node ID $nodeId not found.");
    }

    /**
     * Get tree by type ID
     *
     * @param int    $typeId
     * @param string $type
     *
     * @return TreeInterface[]|WritableTreeInterface[]
     * @throws NodeNotFoundException
     */
    public function getByTypeId($typeId, $type = null)
    {
        $trees = array();
        foreach ($this->siterootManager->findAll() as $siteroot) {
            $tree = $this->getBySiteRootId($siteroot->getId());

            if ($tree->hasByTypeId($typeId, $type)) {
                $trees[] = $tree;
            }
        }

        return $trees;
    }

    /**
     * @return TreeInterface[]
     */
    public function getAll()
    {
        foreach ($this->siterootManager->findAll() as $siteroot) {
            $this->getBySiteRootId($siteroot->getId());
        }

        return $this->trees;
    }

    /**
     * @param TreeNodeInterface $node
     *
     * @return TreeNodeInterface[]
     */
    public function getInstanceNodes(TreeNodeInterface $node)
    {
        $instanceNodes = array();
        foreach ($this->getAll() as $tree) {
            $instanceNodes = array_merge($instanceNodes, $tree->getInstances($node));
        }

        return $instanceNodes;
    }
}
