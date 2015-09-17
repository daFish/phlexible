<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\Tree;

use Phlexible\Bundle\TreeBundle\Entity\StructureNode;
use Phlexible\Bundle\TreeBundle\Exception\NodeNotFoundException;
use Phlexible\Bundle\TreeBundle\Model\TreeInterface;
use Phlexible\Bundle\TreeBundle\Model\TreeManagerInterface;
use Phlexible\Component\Node\Model\NodeManagerInterface;
use Phlexible\Component\Site\Model\SiteManagerInterface;

/**
 * Tree manager.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class TreeManager implements TreeManagerInterface
{
    /**
     * @var SiteManagerInterface
     */
    private $siterootManager;

    /**
     * @var NodeManagerInterface
     */
    private $nodeManager;

    /**
     * @var TreeFactoryInterface
     */
    private $treeFactory;

    /**
     * @var TreeInterface[]
     */
    private $trees = array();

    /**
     * @param \Phlexible\Component\Site\Model\SiteManagerInterface $siterootManager
     * @param NodeManagerInterface                                 $nodeManager
     * @param TreeFactoryInterface                                 $treeFactory
     */
    public function __construct(
        SiteManagerInterface $siterootManager,
        NodeManagerInterface $nodeManager,
        TreeFactoryInterface $treeFactory
    ) {
        $this->siterootManager = $siterootManager;
        $this->nodeManager = $nodeManager;
        $this->treeFactory = $treeFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getBySiteRootId(TreeContextInterface $treeContext, $siterootId)
    {
        $identifier = $treeContext->getWorkspace().'_'.$treeContext->getLocale().'_'.$siterootId;

        if (!isset($this->trees[$identifier])) {
            $tree = $this->treeFactory->factory($treeContext, $siterootId);
            $this->trees[$identifier] = $tree;
        }

        return $this->trees[$identifier];
    }

    /**
     * {@inheritdoc}
     */
    public function getByNodeId(TreeContextInterface $treeContext, $nodeId)
    {
        foreach ($this->siterootManager->findAll() as $siteroot) {
            $tree = $this->getBySiteRootId($treeContext, $siteroot->getId());

            if ($tree->has($nodeId)) {
                return $tree;
            }
        }

        throw new NodeNotFoundException("Tree for node ID $nodeId not found.");
    }

    /**
     * {@inheritdoc}
     */
    public function getAll(TreeContextInterface $treeContext)
    {
        foreach ($this->siterootManager->findAll() as $siteroot) {
            $this->getBySiteRootId($treeContext, $siteroot->getId());
        }

        return $this->trees;
    }

    /**
     * {@inheritdoc}
     */
    public function createTree(TreeContextInterface $treeContext, $siterootId, $type, $typeId, $userId)
    {
        $node = new StructureNode();
        $node
            ->setWorkspace('live')
            ->setLocale($treeContext->getLocale())
            ->setSiterootId($siterootId)
            ->setParentNode(null)
            ->setContentType($type)
            ->setContentId($typeId)
            ->setCreateUserId($userId)
            ->setCreatedAt(new \DateTime());

        $this->nodeManager->updateNode($node);

        return $this->getBySiteRootId($treeContext, $siterootId);
    }
}
