<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\Tree;

use Phlexible\Bundle\SiterootBundle\Model\SiterootManagerInterface;
use Phlexible\Bundle\TreeBundle\Model\NodeManagerInterface;
use Phlexible\Bundle\TreeBundle\Model\NodeStateManagerInterface;
use Phlexible\Bundle\TreeBundle\Node\NodeContextFactoryInterface;
use Phlexible\Bundle\TreeBundle\Node\NodeHasherInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Tree factory
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class TreeFactory implements TreeFactoryInterface
{
    /**
     * @var NodeManagerInterface
     */
    private $nodeManager;

    /**
     * @var NodeStateManagerInterface
     */
    private $nodeStateManager;

    /**
     * @var NodeContextFactoryInterface
     */
    private $nodeContextFactory;

    /**
     * @var NodeHasherInterface
     */
    private $nodeHasher;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @param NodeManagerInterface        $nodeManager
     * @param NodeStateManagerInterface   $nodeStateManager
     * @param NodeContextFactoryInterface $nodeContextFactory
     * @param NodeHasherInterface         $nodeHasher
     * @param EventDispatcherInterface    $eventDispatcher
     */
    public function __construct(
        NodeManagerInterface $nodeManager,
        NodeStateManagerInterface $nodeStateManager,
        NodeContextFactoryInterface $nodeContextFactory,
        NodeHasherInterface $nodeHasher,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->nodeManager = $nodeManager;
        $this->nodeStateManager = $nodeStateManager;
        $this->nodeContextFactory = $nodeContextFactory;
        $this->nodeHasher = $nodeHasher;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function factory($siteRootId)
    {
        return new Tree(
            $siteRootId,
            $this->nodeManager,
            $this->nodeStateManager,
            $this->nodeContextFactory,
            $this->nodeHasher,
            $this->eventDispatcher
        );
    }
}
