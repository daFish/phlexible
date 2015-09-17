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
use Phlexible\Component\Message\Model\MessageManagerInterface;

/**
 * Message manager handler.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class MessageManagerHandler implements HandlerInterface
{
    /**
     * @var MessageManagerInterface
     */
    private $messageManager;

    /**
     * @param MessageManagerInterface $messageManager
     */
    public function __construct(MessageManagerInterface $messageManager)
    {
        $this->messageManager = $messageManager;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(Message $message)
    {
        $this->messageManager->updateMessage($message);
    }

    /**
     * {@inheritdoc}
     */
    public function close()
    {
    }
}
