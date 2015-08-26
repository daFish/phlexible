<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\GuiBundle\Poller;

/**
 * Poller message collection
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class MessageCollection
{
    /**
     * @var string
     */
    private $userId;

    /**
     * @var \DateTime|null
     */
    private $lastPoll;

    /**
     * @var Message[]
     */
    private $messages = array();

    /**
     * @param string    $userId
     * @param \DateTime $lastPoll
     * @param Message[] $messages
     */
    public function __construct($userId, \DateTime $lastPoll = null, array $messages = array())
    {
        $this->userId = $userId;
        $this->lastPoll = $lastPoll;

        foreach ($messages as $message) {
            $this->add($message);
        }
    }

    /**
     * @return string
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @return \DateTime|null
     */
    public function getLastPoll()
    {
        return $this->lastPoll;
    }

    /**
     * @param Message $message
     *
     * @return $this
     */
    public function add(Message $message)
    {
        $this->messages[] = $message;

        return $this;
    }

    /**
     * @return Message[]
     */
    public function all()
    {
        return $this->messages;
    }
}
