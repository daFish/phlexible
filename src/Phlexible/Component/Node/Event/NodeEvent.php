<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\Node\Event;

use Phlexible\Component\Node\Model\NodeInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Node event
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
     * Return node
     *
     * @return \Phlexible\Component\Node\Model\NodeInterface
     */
    public function getNode()
    {
        return $this->node;
    }
}
