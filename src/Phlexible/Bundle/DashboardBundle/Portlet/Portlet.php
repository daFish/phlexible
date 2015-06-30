<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\DashboardBundle\Portlet;

/**
 * Portlet
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class Portlet
{
    /**
     * @return array
     */
    public function getData()
    {
        return array();
    }

    /**
     * @return array
     */
    public function getSettings()
    {
        return array();
    }
}
