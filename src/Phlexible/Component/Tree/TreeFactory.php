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

use Phlexible\Bundle\TreeBundle\Node\NodeContextFactoryInterface;
use Phlexible\Bundle\TreeBundle\Node\NodeHasherInterface;
use Phlexible\Component\Node\Model\NodeManagerInterface;
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
     * @param NodeContextFactoryInterface $nodeContextFactory
     * @param NodeHasherInterface         $nodeHasher
     * @param EventDispatcherInterface    $eventDispatcher
     */
    public function __construct(
        NodeManagerInterface $nodeManager,
        NodeContextFactoryInterface $nodeContextFactory,
        NodeHasherInterface $nodeHasher,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->nodeManager = $nodeManager;
        $this->nodeContextFactory = $nodeContextFactory;
        $this->nodeHasher = $nodeHasher;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function factory(TreeContextInterface $treeContext, $siteRootId)
    {
        return new Tree(
            $treeContext,
            $siteRootId,
            $this->nodeManager,
            $this->nodeContextFactory,
            $this->nodeHasher,
            $this->eventDispatcher
        );
    }
}
