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
 * Reorder child nodes context event
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ReorderChildNodesContextEvent extends NodeContextEvent
{
    /**
     * @var array
     */
    private $sortIds;

    /**
     * @param NodeContext $node
     * @param array       $sortIds
     */
    public function __construct(NodeContext $node, array $sortIds)
    {
        parent::__construct($node);

        $this->sortIds = $sortIds;
    }

    /**
     * @return array
     */
    public function getSortIds()
    {
        return $this->sortIds;
    }
}
