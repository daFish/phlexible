<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\CmsBundle\EventListener;

use Phlexible\Bundle\CmsBundle\Usage\UsageUpdater;
use Phlexible\Bundle\TreeBundle\TreeEvents;
use Phlexible\Component\Node\Event\NodeEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Node listener.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class NodeListener implements EventSubscriberInterface
{
    /**
     * @var UsageUpdater
     */
    private $usageUpdater;

    /**
     * @param UsageUpdater $usageUpdater
     */
    public function __construct(UsageUpdater $usageUpdater)
    {
        $this->usageUpdater = $usageUpdater;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            TreeEvents::CREATE_NODE_CONTEXT => 'onCreateNode',
            TreeEvents::UPDATE_NODE_CONTEXT => 'onUpdateNode',
        );
    }

    /**
     * @param NodeEvent $event
     */
    public function onCreateNode(NodeEvent $event)
    {
        $this->usageUpdater->updateUsage($event->getNode());
    }

    /**
     * @param NodeEvent $event
     */
    public function onUpdateNode(NodeEvent $event)
    {
        $this->usageUpdater->updateUsage($event->getNode());
    }
}
