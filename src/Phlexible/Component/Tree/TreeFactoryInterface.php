<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\Tree;

use Phlexible\Bundle\TreeBundle\Model\TreeInterface;

/**
 * Tree factory
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
