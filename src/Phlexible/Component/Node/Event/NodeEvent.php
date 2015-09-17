<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\Node\Event;

use Phlexible\Component\Node\Model\NodeInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Node event.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class NodeEvent extends Event
{
    /**
     * @var NodeInterface
     */
    private $node;

    /**
     * @param NodeInterface $node
     */
    public function __construct(NodeInterface $node)
    {
        $this->node = $node;
    }

    /**
     * Return node.
     *
     * @return \Phlexible\Component\Node\Model\NodeInterface
     */
    public function getNode()
    {
        return $this->node;
    }
}
