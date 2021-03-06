<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementBundle\EventListener;

use Phlexible\Bundle\ElementBundle\Util\SuggestFieldUtil;
use Phlexible\Bundle\ElementBundle\Util\SuggestMetaFieldUtil;
use Phlexible\Component\Suggest\Event\GarbageCollectEvent;
use Phlexible\Component\Suggest\SuggestEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Datasource listener.
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
     * @var SuggestMetaFieldUtil
     */
    private $suggestMetaFieldUtil;

    /**
     * @param SuggestFieldUtil     $suggestFieldUtil
     * @param SuggestMetaFieldUtil $suggestMetaFieldUtil
     */
    public function __construct(SuggestFieldUtil $suggestFieldUtil, SuggestMetaFieldUtil $suggestMetaFieldUtil)
    {
        $this->suggestFieldUtil = $suggestFieldUtil;
        $this->suggestMetaFieldUtil = $suggestMetaFieldUtil;
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
     * @param GarbageCollectEvent $event
     */
    public function onGarbageCollect(GarbageCollectEvent $event)
    {
        // get id of data source to process
        $values = $event->getDataSourceValueBag();
        $datasource = $values->getDatasource();
        $datasourceId = $datasource->getId();
        $language = $values->getLanguage();

        // fetch all data source values used in element online versions
        $onlineValues = $this->suggestFieldUtil->fetchUsedValues($datasourceId, $language);

        // remove offline values from collection
        $event->markActive($onlineValues);

        // fetch all data source values used in element online versions
        $onlineMetaValues = $this->suggestMetaFieldUtil->fetchUsedValues($datasourceId, $language);

        // remove offline values from collection
        $event->markActive($onlineMetaValues);
    }
}
