<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\MessageBundle\EventListener;

use Phlexible\Bundle\MessageBundle\Event\MessageEvent;
use Phlexible\Bundle\MessageBundle\Handler\HandlerInterface;
use Phlexible\Bundle\MessageBundle\MessageEvents;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Message listener
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class MessageListener implements EventSubscriberInterface
{
    /**
     * @var HandlerInterface
     */
    private $messageHandler;

    /**
     * @param HandlerInterface $messageHandler
     */
    public function __construct(HandlerInterface $messageHandler)
    {
        $this->messageHandler = $messageHandler;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            MessageEvents::MESSAGE => 'onMessage',
            KernelEvents::TERMINATE => 'onTerminate',
            ConsoleEvents::TERMINATE => 'onTerminate',
        ];
    }

    /**
     * @param MessageEvent $event
     */
    public function onMessage(MessageEvent $event)
    {
        $message = $event->getMessage();

        $this->messageHandler->handle($message);
    }

    /**
     * On terminate
     */
    public function onTerminate()
    {
        $this->messageHandler->close();
    }
}
