<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\NodeType\Model;

use Phlexible\Bundle\TreeBundle\Node\NodeContext;

/**
 * Node type manager interface
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
