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
