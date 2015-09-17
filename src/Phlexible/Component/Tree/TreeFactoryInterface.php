<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\Tree;

use Phlexible\Bundle\TreeBundle\Model\TreeInterface;

/**
 * Tree factory.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface TreeFactoryInterface
{
    /**
     * @param TreeContextInterface $treeContext
     * @param string               $siteRootId
     *
     * @return TreeInterface
     */
    public function factory(TreeContextInterface $treeContext, $siteRootId);
}
