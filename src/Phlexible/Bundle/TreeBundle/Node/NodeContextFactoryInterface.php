<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\TreeBundle\Node;

use Phlexible\Bundle\TreeBundle\Model\TreeInterface;
use Phlexible\Component\Node\Model\NodeInterface;

/**
 * Node context factory interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface NodeContextFactoryInterface
{
    /**
     * @param TreeInterface $tree
     * @param \Phlexible\Component\Node\Model\NodeInterface $node
     * @param string        $language
     *
     * @return NodeContext
     */
    public function factory(TreeInterface $tree, NodeInterface $node, $language);
}
