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
 * Node ID path generator
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class NodeIdPathDecorator implements PathDecoratorInterface
{
    /**
     * @var string
     */
    private $separator;

    /**
     * @param string $separator
     */
    public function __construct($separator = '.')
    {
        $this->separator = $separator;
    }

    /**
     * {@inheritdoc}
     */
    public function decoratePath(Path $path, NodeContext $node, $language)
    {
        // add tid and postfix
        $path->append($this->separator . $node->getId());
    }
}
