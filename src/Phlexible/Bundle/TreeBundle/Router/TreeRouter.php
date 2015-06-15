<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\TreeBundle\Router;

use Phlexible\Bundle\TreeBundle\Exception\BadMethodCallException;
use Phlexible\Bundle\TreeBundle\Model\TreeNodeInterface;
use Symfony\Cmf\Bundle\RoutingBundle\Routing\DynamicRouter;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Matcher\RequestMatcherInterface;
use Symfony\Component\Routing\Matcher\UrlMatcherInterface;
use Symfony\Component\Routing\RequestContext;

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
        return $name instanceof TreeNodeInterface;
    }

    /**
     * {@inheritdoc}
     */
    public function match($pathinfo)
    {
        throw new BadMethodCallException('match() not supported.');
    }
}
