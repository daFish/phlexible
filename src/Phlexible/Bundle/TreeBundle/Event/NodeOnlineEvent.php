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
class NodeOnlineEvent extends Event
{
    /**
     * @var NodeState
     */
    private $nodeOnline;

    /**
     * @param NodeState $nodeOnline
     */
    public function __construct(NodeState $nodeOnline)
    {
        $this->nodeOnline = $nodeOnline;
    }

    /**
     * Return node
     *
     * @return NodeState
     */
    public function getNodeOnline()
    {
        return $this->nodeOnline;
    }
}
