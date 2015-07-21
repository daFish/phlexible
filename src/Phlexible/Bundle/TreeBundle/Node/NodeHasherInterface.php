<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\Node;

/**
 * Node hasher interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface NodeHasherInterface
{
    /**
     * @param NodeContext $node
     * @param int         $version
     * @param string      $language
     *
     * @return string
     */
    public function hashNode(NodeContext $node, $version, $language);
}
