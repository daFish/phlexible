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
 * Reorder node event
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ReorderNodeContextEvent extends NodeContextEvent
{
    /**
     * @var int
     */
    private $sort;

    /**
     * @param NodeContext $node
     * @param int         $sort
     */
    public function __construct(NodeContext $node, $sort)
    {
        parent::__construct($node);

        $this->sort = $sort;
    }

    /**
     * @return int
     */
    public function getSort()
    {
        return $this->sort;
    }
}
