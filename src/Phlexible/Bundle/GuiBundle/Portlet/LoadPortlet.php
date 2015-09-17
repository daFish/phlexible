<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\GuiBundle\Portlet;

use Phlexible\Bundle\DashboardBundle\Domain\Portlet;

/**
 * Load lortlet.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class LoadPortlet extends Portlet
{
    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        $l1 = 0;
        $l5 = 0;
        $l15 = 0;
        if (function_exists('sys_getloadavg')) {
            $l = sys_getloadavg();
            $l1 = $l[0];
            $l5 = $l[1];
            $l15 = $l[2];
        }
        $data = array('l1' => $l1, 'l5' => $l5, 'l15' => $l15, 'ts' => time());

        return $data;
    }
}
