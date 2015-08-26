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
