<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\Message\Message;

use Phlexible\Component\Message\Domain\Message;
use Phlexible\Component\Message\Event\MessageEvent;
use Phlexible\Component\Message\MessageEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Message poster.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class MessagePoster
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
     * @param Message $message
     */
    public function post(Message $message)
    {
        $event = new MessageEvent($message);
        $this->dispatcher->dispatch(MessageEvents::MESSAGE, $event);
    }
}
