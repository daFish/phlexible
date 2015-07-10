<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaManagerBundle\EventListener;

use Phlexible\Bundle\DataSourceBundle\Entity\DataSourceValueBag;
use Phlexible\Component\MediaManager\Util\SuggestFieldUtil;
use Phlexible\Component\Suggest\SuggestEvents;
use Phlexible\Component\Suggest\Event\GarbageCollectEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Data source listener
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class DatasourceListener implements EventSubscriberInterface
{
    /**
     * @var SuggestFieldUtil
     */
    private $suggestFieldUtil;

    /**
     * @param SuggestFieldUtil $suggestFieldUtil
     */
    public function __construct(SuggestFieldUtil $suggestFieldUtil)
    {
        $this->suggestFieldUtil = $suggestFieldUtil;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            SuggestEvents::GARBAGE_COLLECT => 'onGarbageCollect',
        );
    }

    /**
     * Ensure used values are marked active.
     *
     * @param \Phlexible\Component\Suggest\Event\GarbageCollectEvent $event
     */
    public function onGarbageCollect(GarbageCollectEvent $event)
    {
        $values = $this->fetchValues($event->getDataSourceValueBag());

        $event->markActive($values);
    }

    /**
     * Ensure used values are not deleted from data source.
     *
     * @param DataSourceValueBag $values
     *
     * @return array
     */
    private function fetchValues(DataSourceValueBag $values)
    {
        $language = $values->getLanguage();

        return $this->suggestFieldUtil->fetchUsedValues($values, array($language));
    }
}
