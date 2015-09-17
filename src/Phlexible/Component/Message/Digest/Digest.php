<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\Message\Digest;

use DateTime;
use Phlexible\Component\Message\Domain\Message;
use Phlexible\Component\MessageFilter\Domain\Filter;
use Phlexible\Component\MessageSubscription\Domain\Subscription;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Digest.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class Digest
{
    /**
     * @var UserInterface
     */
    private $user;

    /**
     * @var Filter
     */
    private $filter;

    /**
     * @var \Phlexible\Component\MessageSubscription\Domain\Subscription
     */
    private $subscription;

    /**
     * @var DateTime
     */
    private $lastSend;

    /**
     * @var Message[]
     */
    private $messages;

    /**
     * @param UserInterface                                                $user
     * @param \Phlexible\Component\MessageFilter\Domain\Filter             $filter
     * @param \Phlexible\Component\MessageSubscription\Domain\Subscription $subscription
     * @param DateTime                                                     $lastSend
     * @param Message[]                                                    $messages
     */
    public function __construct(
        UserInterface $user,
        Filter $filter,
        Subscription $subscription,
        DateTime $lastSend,
        array $messages
    ) {
        $this->user = $user;
        $this->filter = $filter;
        $this->subscription = $subscription;
        $this->lastSend = $lastSend;
        foreach ($messages as $message) {
            $this->addMessage($message);
        }
    }

    /**
     * @return UserInterface
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return \Phlexible\Component\MessageFilter\Domain\Filter
     */
    public function getFilter()
    {
        return $this->filter;
    }

    /**
     * @return \Phlexible\Component\MessageSubscription\Domain\Subscription
     */
    public function getSubscription()
    {
        return $this->subscription;
    }

    /**
     * @return \Phlexible\Component\MessageSubscription\Domain\Subscription
     */
    public function getLastSend()
    {
        return $this->lastSend;
    }

    /**
     * @return Message[]
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * @param Message $message
     */
    private function addMessage(Message $message)
    {
        $this->messages[] = $message;
    }
}
