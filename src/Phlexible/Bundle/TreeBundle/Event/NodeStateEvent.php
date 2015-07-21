<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\Event;

use Phlexible\Bundle\TreeBundle\Entity\NodeState;
use Symfony\Component\EventDispatcher\Event;

/**
 * Node online event
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class NodeStateEvent extends Event
{
    /**
     * @var NodeState
     */
    private $nodeState;

    /**
     * @param NodeState $nodeState
     */
    public function __construct(NodeState $nodeState)
    {
        $this->nodeState = $nodeState;
    }

    /**
     * Return node
     *
     * @return NodeState
     */
    public function getNodeState()
    {
        return $this->nodeState;
    }
}
