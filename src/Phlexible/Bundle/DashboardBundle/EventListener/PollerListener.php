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

use Phlexible\Bundle\DashboardBundle\Domain\PortletCollection;
use Phlexible\Bundle\GuiBundle\Event\PollEvent;
use Phlexible\Bundle\GuiBundle\Poller\Message;

/**
 * Poll listener
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class PollerListener
{
    /**
     * @var PortletCollection
     */
    private $portlets;

    /**
     * @param PortletCollection $portlets
     */
    public function __construct(PortletCollection $portlets)
    {
        $this->portlets = $portlets;
    }

    /**
     * @param PollEvent $event
     */
    public function onPoll(PollEvent $event)
    {
        $messages = $event->getMessages();

        $data = array();
        foreach ($this->portlets->all() as $portletId => $portlet) {
            $data[$portletId] = $portlet->getData();
        }

        $messages->add(new Message('dashboard', 'update', $data));
    }
}
