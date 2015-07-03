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
use Psr\Log\LoggerInterface;

/**
 * Log handler
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class LogHandler implements HandlerInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(Message $message)
    {
        $type    = $message->getType();
        $channel = $message->getChannel();
        $role    = $message->getRole();
        $subject = $message->getSubject();
        $body    = $message->getBody();

        // build message
        $msg = "Message ($type)";

        if (!empty($channel)) {
            $msg .= ' in channel ' . $channel;
        }

        if (!empty($role)) {
            $msg .= ' with role ' . $role;
        }

        $msg .= ': ' . $subject;

        $methodMap = array(
            Message::TYPE_INFO => 'info',
            Message::TYPE_ERROR => 'error',
        );
        $method = $methodMap[$type];

        // log message
        if ($type >= Message::TYPE_ERROR && !empty($body)) {
            $msg .= PHP_EOL . $body;
        }

        $this->logger->$method($msg);
    }

    /**
     * {@inheritdoc}
     */
    public function close()
    {
    }
}
