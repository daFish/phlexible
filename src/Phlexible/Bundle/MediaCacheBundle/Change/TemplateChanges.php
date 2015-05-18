<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaCacheBundle\Change;

use Phlexible\Component\MediaCache\Model\CacheManagerInterface;
use Phlexible\Component\MediaCache\Queue\BatchBuilder;
use Phlexible\Component\MediaCache\Queue\BatchResolver;
use Phlexible\Component\MediaCache\Queue\Queue;
use Phlexible\Component\MediaCache\Queue\QueueProcessor;
use Phlexible\Component\MediaTemplate\Model\TemplateManagerInterface;
use Phlexible\Component\Volume\Model\VolumeManagerInterface;

/**
 * Template changes
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class TemplateChanges
{
    /**
     * @var TemplateManagerInterface
     */
    private $templateManager;

    /**
     * @var CacheManagerInterface
     */
    private $cacheManager;

    /**
     * @var VolumeManagerInterface
     */
    private $volumeManager;

    /**
     * @var BatchBuilder
     */
    private $batchBuilder;

    /**
     * @var BatchResolver
     */
    private $batchResolver;

    /**
     * @var QueueProcessor
     */
    private $queueProcessor;

    /**
     * @param TemplateManagerInterface $templateManager
     * @param CacheManagerInterface    $cacheManager
     * @param VolumeManagerInterface   $volumeManager
     * @param BatchBuilder             $batchBuilder
     * @param BatchResolver            $batchResolver
     * @param QueueProcessor           $queueProcessor
     */
    public function __construct(
        TemplateManagerInterface $templateManager,
        CacheManagerInterface $cacheManager,
        VolumeManagerInterface $volumeManager,
        BatchBuilder $batchBuilder,
        BatchResolver $batchResolver,
        QueueProcessor $queueProcessor
    )
    {
        $this->templateManager = $templateManager;
        $this->cacheManager = $cacheManager;
        $this->volumeManager = $volumeManager;
        $this->batchBuilder = $batchBuilder;
        $this->batchResolver = $batchResolver;
        $this->queueProcessor = $queueProcessor;
    }

    /**
     * @return Change[]
     */
    public function changes()
    {
        $changes = [];

        foreach ($this->templateManager->findAll() as $template) {
            $cacheItems = $this->cacheManager->findOutdatedTemplates($template);

            foreach ($cacheItems as $cacheItem) {
                $volume = $this->volumeManager->getByFileId($cacheItem->getFileId());
                $file = $volume->findFile($cacheItem->getFileId(), $cacheItem->getFileVersion());
                $template = $this->templateManager->find($cacheItem->getTemplateKey());
                $change = new Change($file, $template, $cacheItem->getTemplateRevision());

                $changes[] = $change;
            }
        }

        return $changes;
    }

    /**
     * @param bool $viaQueue
     */
    public function commit($viaQueue = false)
    {
        $changes = $this->changes();

        $queue = new Queue();
        foreach ($changes as $change) {
            $batch = $this->batchBuilder->createForTemplateAndFile($change->getTemplate(), $change->getFile());
            $changeQueue = $this->batchResolver->resolve($batch);
            $queue->merge($changeQueue);
        }

        foreach ($queue->all() as $queueItem) {
            if ($viaQueue) {
                $this->cacheManager->updateCacheItem($queueItem);
            } else {
                $this->queueProcessor->processItem($queueItem);
            }
        }
    }
}
