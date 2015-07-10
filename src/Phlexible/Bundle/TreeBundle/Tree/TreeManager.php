<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\Tree;

use Phlexible\Bundle\SiterootBundle\Model\SiterootManagerInterface;
use Phlexible\Bundle\TreeBundle\Entity\StructureElementNode;
use Phlexible\Bundle\TreeBundle\Exception\NodeNotFoundException;
use Phlexible\Bundle\TreeBundle\Mediator\TreeMediatorInterface;
use Phlexible\Bundle\TreeBundle\Model\NodeManagerInterface;
use Phlexible\Bundle\TreeBundle\Model\TreeInterface;
use Phlexible\Bundle\TreeBundle\Model\TreeManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Tree manager
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class TreeManager implements TreeManagerInterface
{
    /**
     * @var SiterootManagerInterface
     */
    private $siterootManager;

    /**
     * @var NodeManagerInterface
     */
    private $nodeManager;

    /**
     * @var TreeMediatorInterface
     */
    private $mediator;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var TreeInterface[]
     */
    private $trees = array();

    /**
     * @param SiterootManagerInterface $siterootManager
     * @param NodeManagerInterface     $nodeManager
     * @param TreeMediatorInterface    $mediator
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        SiterootManagerInterface $siterootManager,
        NodeManagerInterface $nodeManager,
        TreeMediatorInterface $mediator,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->siterootManager = $siterootManager;
        $this->nodeManager = $nodeManager;
        $this->mediator = $mediator;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function getBySiteRootId($siteRootId)
    {
        if (!isset($this->trees[$siteRootId])) {
            $tree = new Tree($siteRootId, $this->nodeManager, $this->mediator, $this->eventDispatcher);
            $this->trees[$siteRootId] = $tree;
        }

        return $this->trees[$siteRootId];
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function getAll()
    {
        foreach ($this->siterootManager->findAll() as $siteroot) {
            $this->getBySiteRootId($siteroot->getId());
        }

        return $this->trees;
    }

    /**
     * {@inheritdoc}
     */
    public function createTree($siterootId, $type, $typeId, $userId)
    {
        $node = new StructureElementNode();
        $node
            ->setSiterootId($siterootId)
            ->setParentNode(null)
            ->setType($type)
            ->setTypeId($typeId)
            ->setCreateUserId($userId)
            ->setCreatedAt(new \DateTime);

        $this->nodeManager->updateNode($node);

        return $this->getBySiteRootId($siterootId);
    }
}
