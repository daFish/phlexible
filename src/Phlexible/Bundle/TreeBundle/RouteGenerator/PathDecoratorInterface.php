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
 * Path decorator interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface PathDecoratorInterface
{
    /**
     * @param Path        $path
     * @param NodeContext $node
     * @param string      $language
     */
    public function decoratePath(Path $path, NodeContext $node, $language);
}
