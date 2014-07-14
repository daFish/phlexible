<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaManagerBundle\EventListener;

use Phlexible\Bundle\DataSourceBundle\DataSourceEvents;
use Phlexible\Bundle\DataSourceBundle\Entity\DataSource;
use Phlexible\Bundle\DataSourceBundle\Entity\DataSourceValueBag;
use Phlexible\Bundle\DataSourceBundle\Event\CollectionEvent;
use Phlexible\Bundle\DataSourceBundle\Value\ValueCollection;
use Phlexible\Bundle\MediaManagerBundle\Util\SuggestFieldUtil;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Data source listener
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class DataSourceListener implements EventSubscriberInterface
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
            DataSourceEvents::MARK_ACTIVE          => 'onMarkActive',
            DataSourceEvents::BEFORE_MARK_INACTIVE => 'onBeforeMarkInactive',
            DataSourceEvents::BEFORE_DELETE_VALUES => 'onBeforeDeleteValues',
        );
    }

    /**
     * Ensure used values are marked active.
     *
     * @param CollectionEvent $event
     */
    public function onMarkActive(CollectionEvent $event)
    {
        $values = $this->fetchValues($event->getDataSourceValueBag());

        $event->getCollection()->addValues($values);
    }

    /**
     * @param CollectionEvent $event
     */
    public function onBeforeMarkInactive(CollectionEvent $event)
    {
        $values = $this->fetchValues($event->getDataSourceValueBag());

        $event->getCollection()->removeValues($values);
    }

    /**
     * @param CollectionEvent $event
     */
    public function onBeforeDeleteValues(CollectionEvent $event)
    {
        $values = $this->fetchValues($event->getDataSourceValueBag());

        $event->getCollection()->removeValues($values);

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
