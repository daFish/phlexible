<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\Event;

use Phlexible\Bundle\TreeBundle\Node\NodeContext;

/**
 * Move node context event
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
