<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\SiterootBundle\EventListener;

use Phlexible\Bundle\MessageBundle\Entity\Message;
use Phlexible\Bundle\MessageBundle\Message\MessagePoster;
use Phlexible\Bundle\SiterootBundle\Event\SiterootEvent;
use Phlexible\Bundle\SiterootBundle\SiterootEvents;
use Phlexible\Bundle\SiterootBundle\SiterootMessage;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

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
            SiterootEvents::CREATE_SITEROOT => 'onCreateSiteroot',
            SiterootEvents::UPDATE_SITEROOT => 'onUpdateSiteroot',
            SiterootEvents::DELETE_SITEROOT => 'onDeleteSiteroot',
        );
    }

    /**
     * @param SiterootEvent $event
     */
    public function onCreateSiteroot(SiterootEvent $event)
    {
        $siteroot = $event->getSiteroot();

        $message = SiterootMessage::create(
            "Siteroot {$siteroot->getTitle()} created."
        );

        $this->postMessage($message);
    }

    /**
     * @param SiterootEvent $event
     */
    public function onUpdateSiteroot(SiterootEvent $event)
    {
        $siteroot = $event->getSiteroot();

        $message = SiterootMessage::create(
            "Siteroot {$siteroot->getTitle()} updated."
        );

        $this->postMessage($message);
    }

    /**
     * @param SiterootEvent $event
     */
    public function onDeleteSiteroot(SiterootEvent $event)
    {
        $siteroot = $event->getSiteroot();

        $message = SiterootMessage::create(
            "Siteroot {$siteroot->getTitle()} deleted."
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
