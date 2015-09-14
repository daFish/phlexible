<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\TreeBundle\Event;

use Phlexible\Bundle\TreeBundle\Entity\Route;
use Symfony\Component\EventDispatcher\Event;

/**
 * Route event
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class RouteEvent extends Event
{
    /**
     * @var Route
     */
    private $route;

    /**
     * @param Route $route
     */
    public function __construct(Route $route)
    {
        $this->route = $route;
    }

    /**
     * Return route
     *
     * @return Route
     */
    public function getRoute()
    {
        return $this->route;
    }
}
