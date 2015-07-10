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
 * Suffix path decorator
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class SuffixPathDecorator implements PathDecoratorInterface
{
    /**
     * @var string
     */
    private $suffix;

    /**
     * @param string $suffix
     */
    public function __construct($suffix = '.html')
    {
        $this->suffix = $suffix;
    }

    /**
     * {@inheritdoc}
     */
    public function decoratePath(Path $path, NodeContext $node, $language)
    {
        // add tid and postfix
        $path->append($this->suffix);
    }
}
