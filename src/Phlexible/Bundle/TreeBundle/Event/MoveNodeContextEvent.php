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

/**
 * Move node context event.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class MoveNodeContextEvent extends NodeContextEvent
{
    /**
     * @var NodeContext
     */
    private $parentNode;

    /**
     * @param NodeContext $node
     * @param NodeContext $parentNode
     */
    public function __construct(NodeContext $node, NodeContext $parentNode)
    {
        parent::__construct($node);

        $this->parentNode = $parentNode;
    }

    /**
     * @return NodeContext
     */
    public function getParentNode()
    {
        return $this->parentNode;
    }
}
