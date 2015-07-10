<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\ContentTree;

use Phlexible\Bundle\SiterootBundle\Model\SiterootManagerInterface;
use Phlexible\Bundle\TreeBundle\Mediator\TreeMediatorInterface;
use Phlexible\Bundle\TreeBundle\Model\NodeManagerInterface;

/**
 * Delegating content tree manager
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class DelegatingContentTreeManager implements ContentTreeManagerInterface
{
    /**
     * @var SiterootManagerInterface
     */
    private $siterootManager;

    /**
     * @var NodeManagerInterface
     */
    private $treeManager;

    /**
     * @var TreeMediatorInterface
     */
    private $mediator;

    /**
     * @var XmlContentTree[]
     */
    private $trees;

    /**
     * @param SiterootManagerInterface $siterootManager
     * @param NodeManagerInterface     $treeManager
     * @param TreeMediatorInterface        $mediator
     */
    public function __construct(
        SiterootManagerInterface $siterootManager,
        NodeManagerInterface $treeManager,
        TreeMediatorInterface $mediator
    ) {
        $this->siterootManager = $siterootManager;
        $this->treeManager = $treeManager;
        $this->mediator = $mediator;
    }

    /**
     * {@inheritdoc}
     */
    public function findAll()
    {
        if (null === $this->trees) {
            $this->trees = array();
            foreach ($this->siterootManager->findAll() as $siteroot) {
                $tree = $this->treeManager->getBySiteRootId($siteroot->getId());
                $this->trees[] = new DelegatingContentTree($tree, $siteroot, $this->mediator);
            }
        }

        return $this->trees;
    }

    /**
     * {@inheritdoc}
     */
    public function find($siterootId)
    {
        $trees = $this->findAll();
        if (!$trees) {
            return null;
        }

        foreach ($trees as $tree) {
            if ($tree->getSiterootId() === $siterootId) {
                return $tree;
            }
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function findByTreeId($treeId)
    {
        $trees = $this->findAll();
        if (!$trees) {
            return null;
        }

        foreach ($trees as $tree) {
            if ($tree->has($treeId)) {
                return $tree;
            }
        }

        return null;
    }
}
