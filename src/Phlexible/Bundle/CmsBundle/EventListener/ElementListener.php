<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\CmsBundle\EventListener;

use Phlexible\Bundle\CmsBundle\Usage\UsageUpdater;
use Phlexible\Bundle\ElementBundle\ElementEvents;
use Phlexible\Bundle\ElementBundle\Event\ElementVersionEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Element listener
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ElementListener implements EventSubscriberInterface
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
        return [
            ElementEvents::CREATE_ELEMENT_VERSION => 'onCreateElementVersion',
            ElementEvents::UPDATE_ELEMENT_VERSION => 'onUpdateElementVersion',
        ];
    }

    /**
     * @param ElementVersionEvent $event
     */
    public function onCreateElementVersion(ElementVersionEvent $event)
    {
        $this->usageUpdater->updateUsage($event->getElementVersion()->getElement());
    }

    /**
     * @param ElementVersionEvent $event
     */
    public function onUpdateElementVersion(ElementVersionEvent $event)
    {
        $this->usageUpdater->updateUsage($event->getElementVersion()->getElement());
    }
}
