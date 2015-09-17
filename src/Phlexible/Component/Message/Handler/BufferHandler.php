<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\Message\Handler;

use Phlexible\Component\Message\Domain\Message;

/**
 * Buffer handler.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class BufferHandler implements HandlerInterface
{
    /**
     * @var HandlerInterface
     */
    private $handler;

    /**
     * @var Message[]
     */
    private $messages = array();

    /**
     * @param HandlerInterface $handler
     */
    public function __construct(HandlerInterface $handler)
    {
        $this->handler = $handler;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(Message $message)
    {
        $this->messages[] = $message;
    }

    /**
     * @return Message[]
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * {@inheritdoc}
     */
    public function close()
    {
        foreach ($this->messages as $message) {
            $this->handler->handle($message);
        }

        $this->handler->close();
    }
}
