<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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
