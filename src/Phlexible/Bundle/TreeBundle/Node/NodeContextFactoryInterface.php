<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
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
