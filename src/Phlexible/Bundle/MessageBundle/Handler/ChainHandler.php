<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\MessageBundle\Handler;

use Phlexible\Bundle\MessageBundle\Entity\Message;

/**
 * Chain handler
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ChainHandler implements HandlerInterface
{
    /**
     * @var HandlerInterface[]
     */
    private $handlers = array();

    /**
     * @param HandlerInterface[] $handlers
     */
    public function __construct(array $handlers)
    {
        foreach ($handlers as $handler) {
            $this->addHandler($handler);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function addHandler(HandlerInterface $handler)
    {
        $this->handlers[] = $handler;

        return $this;
    }

    /**
     * Will be called as soon as a message is posted.
     *
     * @param Message $message
     */
    public function handle(Message $message)
    {
        foreach ($this->handlers as $handler) {
            $handler->handle($message);
        }
    }

    /**
     * Will be called on kernel/console::terminate event.
     */
    public function close()
    {
        foreach ($this->handlers as $handler) {
            $handler->close();
        }
    }
}
