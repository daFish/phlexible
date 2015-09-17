<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\TreeBundle\Event;

use Phlexible\Bundle\TreeBundle\Node\NodeContext;
use Symfony\Component\EventDispatcher\Event;

/**
 * Node context event.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class NodeContextEvent extends Event
{
    /**
     * @var NodeContext
     */
    private $node;

    /**
     * @param NodeContext $node
     */
    public function __construct(NodeContext $node)
    {
        $this->node = $node;
    }

    /**
     * Return node.
     *
     * @return NodeContext
     */
    public function getNode()
    {
        return $this->node;
    }
}
