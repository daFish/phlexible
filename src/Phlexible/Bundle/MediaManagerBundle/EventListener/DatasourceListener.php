<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\MediaManagerBundle\EventListener;

use Phlexible\Bundle\DataSourceBundle\DataSourceEvents;
use Phlexible\Bundle\DataSourceBundle\Entity\DataSourceValueBag;
use Phlexible\Bundle\DataSourceBundle\Event\GarbageCollectEvent;
use Phlexible\Component\MediaManager\Util\SuggestFieldUtil;
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
        return [
            DataSourceEvents::GARBAGE_COLLECT => 'onGarbageCollect',
        ];
    }

    /**
     * Ensure used values are marked active.
     *
     * @param GarbageCollectEvent $event
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

        return $this->suggestFieldUtil->fetchUsedValues($values, [$language]);
    }
}
