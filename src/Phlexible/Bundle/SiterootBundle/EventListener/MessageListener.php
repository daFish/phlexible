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

use Phlexible\Bundle\SiterootBundle\SiterootMessage;
use Phlexible\Component\Message\Domain\Message;
use Phlexible\Component\Message\Message\MessagePoster;
use Phlexible\Component\Site\Event\SiteEvent;
use Phlexible\Component\Site\SiteEvents;
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
            SiteEvents::CREATE_SITE => 'onCreateSite',
            SiteEvents::UPDATE_SITE => 'onUpdateSite',
            SiteEvents::DELETE_SITE => 'onDeleteSite',
        );
    }

    /**
     * @param SiteEvent $event
     */
    public function onCreateSite(SiteEvent $event)
    {
        $site = $event->getSite();

        $message = SiterootMessage::create(
            "Site {$site->getTitle()} created."
        );

        $this->postMessage($message);
    }

    /**
     * @param SiteEvent $event
     */
    public function onUpdateSite(SiteEvent $event)
    {
        $site = $event->getSite();

        $message = SiterootMessage::create(
            "Site {$site->getTitle()} updated."
        );

        $this->postMessage($message);
    }

    /**
     * @param SiteEvent $event
     */
    public function onDeleteSite(SiteEvent $event)
    {
        $site = $event->getSite();

        $message = SiterootMessage::create(
            "Site {$site->getTitle()} deleted."
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
