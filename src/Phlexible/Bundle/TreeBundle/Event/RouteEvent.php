<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
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
