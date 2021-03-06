<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\DashboardBundle\EventListener;

use Phlexible\Bundle\DashboardBundle\Infobar\InfobarCollection;
use Phlexible\Bundle\GuiBundle\Event\GetConfigEvent;

/**
 * Get config listener.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class GetConfigListener
{
    /**
     * @var InfobarCollection
     */
    private $infobars;

    /**
     * @param InfobarCollection $infobars
     */
    public function __construct(InfobarCollection $infobars)
    {
        $this->infobars = $infobars;
    }

    /**
     * @param GetConfigEvent $event
     */
    public function onGetConfig(GetConfigEvent $event)
    {
        $config = $event->getConfig();

        $infobars = array();
        foreach ($this->infobars->all() as $infobar) {
            $infobars[] = $infobar->toArray();
        }

        $config->set('dashboard.infobars', $infobars);
    }
}
