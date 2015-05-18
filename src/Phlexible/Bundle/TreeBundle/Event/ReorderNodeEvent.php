<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\Event;

use Phlexible\Bundle\TreeBundle\Model\TreeNodeInterface;

/**
 * Reorder node event
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ReorderNodeEvent extends NodeEvent
{
    /**
     * @var int
     */
    private $sort;

    /**
     * @param TreeNodeInterface $node
     * @param int               $sort
     */
    public function __construct(TreeNodeInterface $node, $sort)
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
