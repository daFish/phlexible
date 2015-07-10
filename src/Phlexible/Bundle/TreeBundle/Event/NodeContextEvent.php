<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\Event;

use Phlexible\Bundle\TreeBundle\Node\NodeContext;
use Symfony\Component\EventDispatcher\Event;

/**
 * Node context event
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
     * Return node
     *
     * @return NodeContext
     */
    public function getNode()
    {
        return $this->node;
    }
}
