<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\TreeBundle\Model;

use Phlexible\Bundle\TreeBundle\Entity\Route;

/**
 * Tree manager interface.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface RouteManagerInterface
{
    /**
     * @param int $id
     *
     * @return Route
     */
    public function find($id);

    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return Route
     */
    public function findOneBy(array $criteria, array $orderBy = null);

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param int   $limit
     * @param int   $offset
     *
     * @return Route[]
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null);

    /**
     * @return Route[]
     */
    public function findAll();

    /**
     * @param Route $route
     * @param bool  $flush
     *
     * @return $this
     */
    public function updateRoute(Route $route, $flush = true);

    /**
     * @param Route $route
     *
     * @return $this
     */
    public function deleteRoute(Route $route);
}
