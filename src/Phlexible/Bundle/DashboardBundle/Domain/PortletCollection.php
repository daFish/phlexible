<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\DashboardBundle\Domain;

/**
 * Portlet collection.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class PortletCollection
{
    /**
     * @var array
     */
    private $portlets = array();

    /**
     * @param Portlet[] $portlets
     */
    public function __construct(array $portlets)
    {
        foreach ($portlets as $portletId => $portlet) {
            $this->set($portletId, $portlet);
        }
    }

    /**
     * @param string  $portletId
     * @param Portlet $portlet
     *
     * @return $this
     */
    public function set($portletId, Portlet $portlet)
    {
        $this->portlets[$portletId] = $portlet;

        return $this;
    }

    /**
     * @return Portlet[]
     */
    public function all()
    {
        return $this->portlets;
    }
}
