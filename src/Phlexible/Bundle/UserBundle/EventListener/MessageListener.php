<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\UserBundle\EventListener;

use Phlexible\Bundle\UserBundle\Event\GroupEvent;
use Phlexible\Bundle\UserBundle\Event\UserEvent;
use Phlexible\Bundle\UserBundle\UserEvents;
use Phlexible\Bundle\UserBundle\UserMessage;
use Phlexible\Component\Message\Domain\Message;
use Phlexible\Component\Message\Message\MessagePoster;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Message listener
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class MessageListener implements EventSubscriberInterface
{
    /**
     * @var MessagePoster
     */
    private $messagePoster;

    /**
     * @param MessagePoster $messagePoster
     */
    public function __construct(MessagePoster $messagePoster)
    {
        $this->messagePoster = $messagePoster;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            UserEvents::CREATE_USER => 'onCreateUser',
            UserEvents::UPDATE_USER => 'onUpdateUser',
            UserEvents::DELETE_USER => 'onDeleteUser',
            UserEvents::CREATE_GROUP => 'onCreateGroup',
            UserEvents::UPDATE_GROUP => 'onUpdateGroup',
            UserEvents::DELETE_GROUP => 'onDeleteGroup',
        );
    }

    /**
     * @param UserEvent $event
     */
    public function onCreateUser(UserEvent $event)
    {
        $user = $event->getUser();

        $message = UserMessage::create(
            "User {$user->getUsername()} created."
        );

        $this->postMessage($message);
    }

    /**
     * @param UserEvent $event
     */
    public function onUpdateUser(UserEvent $event)
    {
        $user = $event->getUser();

        $message = UserMessage::create(
            "User {$user->getUsername()} updated."
        );

        $this->postMessage($message);
    }

    /**
     * @param UserEvent $event
     */
    public function onDeleteUser(UserEvent $event)
    {
        $user = $event->getUser();

        $message = UserMessage::create(
            "User {$user->getUsername()} deleted."
        );

        $this->postMessage($message);
    }

    /**
     * @param GroupEvent $event
     */
    public function onCreateGroup(GroupEvent $event)
    {
        $user = $event->getGroup();

        $message = UserMessage::create(
            "Group {$user->getName()} created."
        );

        $this->postMessage($message);
    }

    /**
     * @param GroupEvent $event
     */
    public function onUpdateGroup(GroupEvent $event)
    {
        $user = $event->getGroup();

        $message = UserMessage::create(
            "Group {$user->getName()} updated."
        );

        $this->postMessage($message);
    }

    /**
     * @param GroupEvent $event
     */
    public function onDeleteGroup(GroupEvent $event)
    {
        $user = $event->getGroup();

        $message = UserMessage::create(
            "Group {$user->getName()} deleted."
        );

        $this->postMessage($message);
    }

    /**
     * @param Message $message
     */
    private function postMessage(Message $message)
    {
        $this->messagePoster->post($message);
    }
}
