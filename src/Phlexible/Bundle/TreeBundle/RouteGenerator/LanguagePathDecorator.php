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
 * Language path decorator
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class LanguagePathDecorator implements PathDecoratorInterface
{
    /**
     * {@inheritdoc}
     */
    public function decoratePath(Path $path, NodeContext $node, $language)
    {
        // add language
        $path->prepend('/' . $language);
    }
}
