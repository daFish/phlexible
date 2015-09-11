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
 * Debug handler
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class DebugHandler implements HandlerInterface
{
    /**
     * @var array
     */
    private $messages = array();

    /**
     * {@inheritdoc}
     */
    public function handle(Message $message)
    {
        $typeNames = array(0 => 'info', 1 => 'error');

        $this->messages[] = array(
            'subject'      => $message->getSubject(),
            'body'         => $message->getBody(),
            'type'         => $message->getType(),
            'typeName'     => $typeNames[$message->getType()],
            'channel'      => $message->getChannel(),
            'role'         => $message->getRole(),
            'user'         => $message->getUser(),
            'createdAt'    => $message->getCreatedAt()->format('Y-m-d H:i:s'),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function close()
    {
    }

    /**
     * @return array
     */
    public function getMessages()
    {
        return $this->messages;
    }
}
