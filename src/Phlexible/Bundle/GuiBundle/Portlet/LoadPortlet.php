<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\GuiBundle\Portlet;

use Phlexible\Bundle\DashboardBundle\Portlet\Portlet;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Load lortlet
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class LoadPortlet extends Portlet
{
    /**
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this
            ->setId('load-portlet')
            ->setTitle($translator->trans('gui.server_load', [], 'gui'))
            ->setXtype('gui-load-portlet')
            ->setIconClass('p-gui-load-icon');
    }

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
        $data = [['l1' => $l1, 'l5' => $l5, 'l15' => $l15, 'point' => 0, 'ts' => time()]];

        return $data;
    }
}
