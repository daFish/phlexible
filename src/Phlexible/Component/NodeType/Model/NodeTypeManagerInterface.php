<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\NodeType\Model;

use Phlexible\Bundle\TreeBundle\Node\NodeContext;

/**
 * Node type manager interface.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface NodeTypeManagerInterface
{
    /**
     * @return array
     */
    public function getTypes();

    /**
     * @param NodeContext $node
     *
     * @return array
     */
    public function getTypesForNode(NodeContext $node);
}
