<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\RouteGenerator;

use Phlexible\Bundle\TreeBundle\Node\NodeContext;

/**
 * Path generator interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface PathGeneratorInterface
{
    /**
     * @param NodeContext $node
     * @param string      $language
     *
     * @return string
     */
    public function generatePath(NodeContext $node, $language);
}
