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
