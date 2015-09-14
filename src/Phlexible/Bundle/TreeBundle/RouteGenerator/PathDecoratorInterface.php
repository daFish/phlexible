<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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
