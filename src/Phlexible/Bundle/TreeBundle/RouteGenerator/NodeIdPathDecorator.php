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
