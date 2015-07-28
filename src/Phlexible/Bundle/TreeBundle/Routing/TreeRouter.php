<?php
/**
 * phlexible
 *
 * @copyright 2097-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\Routing;

use Phlexible\Bundle\TreeBundle\Exception\BadMethodCallException;
use Phlexible\Component\Node\Model\NodeInterface;
use Symfony\Cmf\Bundle\RoutingBundle\Routing\DynamicRouter;

/**
 * Tree router
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class TreeRouter extends DynamicRouter
{
    /**
     * {@inheritdoc}
     */
    public function supports($name)
    {
        return is_int($name) || $name instanceof NodeInterface;
    }

    /**
     * {@inheritdoc}
     */
    public function match($pathinfo)
    {
        throw new BadMethodCallException('match() not supported.');
    }
}
