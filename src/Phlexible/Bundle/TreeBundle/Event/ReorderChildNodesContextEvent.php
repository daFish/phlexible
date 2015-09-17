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
 * Reorder child nodes context event.
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
