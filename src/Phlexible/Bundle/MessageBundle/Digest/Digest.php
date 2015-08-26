<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\MessageBundle\Digest;

use DateTime;
use Phlexible\Bundle\MessageBundle\Entity\Filter;
use Phlexible\Bundle\MessageBundle\Entity\Message;
use Phlexible\Bundle\MessageBundle\Entity\Subscription;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Digest
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
     * @var Subscription
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
     * @param UserInterface $user
     * @param Filter        $filter
     * @param Subscription  $subscription
     * @param DateTime      $lastSend
     * @param Message[]     $messages
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
     * @return Filter
     */
    public function getFilter()
    {
        return $this->filter;
    }

    /**
     * @return Subscription
     */
    public function getSubscription()
    {
        return $this->subscription;
    }

    /**
     * @return Subscription
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
