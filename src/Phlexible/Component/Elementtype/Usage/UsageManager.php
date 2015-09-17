<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\Elementtype\Usage;

use Phlexible\Component\Elementtype\Domain\Elementtype;
use Phlexible\Component\Elementtype\ElementtypeEvents;
use Phlexible\Component\Elementtype\Event\ElementtypeUsageEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Usage manager.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class UsageManager
{
    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param \Phlexible\Component\Elementtype\Domain\Elementtype $elementtype
     *
     * @return Usage[]
     */
    public function getUsage(Elementtype $elementtype)
    {
        $event = new ElementtypeUsageEvent($elementtype);
        $this->dispatcher->dispatch(ElementtypeEvents::USAGE, $event);

        return $event->getUsage();
    }
}
