<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\MediaCache\Queue;

use Phlexible\Bundle\GuiBundle\Properties\Properties;
use Phlexible\Bundle\MediaCacheBundle\Entity\CacheItem;
use Phlexible\Bundle\MediaCacheBundle\Exception\AlreadyRunningException;
use Phlexible\Component\MediaCache\Queue as BaseQueue;
use Phlexible\Component\MediaCache\Worker\WorkerInterface;
use Phlexible\Component\MediaTemplate\Model\TemplateManagerInterface;
use Phlexible\Component\Volume\VolumeManager;
use Phlexible\Component\MediaType\Model\MediaTypeManagerInterface;
use Phlexible\Component\Volume\Model\VolumeManagerInterface;
use Symfony\Component\Filesystem\LockHandler;
use Temp\MediaClassifier\MediaClassifier;

/**
 * Queue processor
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class QueueProcessor
{
    /**
     * @var WorkerInterface
     */
    private $worker;

    /**
     * @var VolumeManagerInterface
     */
    private $volumeManager;

    /**
     * @var TemplateManagerInterface
     */
    private $templateManager;

    /**
     * @var MediaClassifier
     */
    private $mediaClassifier;

    /**
     * @var Properties
     */
    private $properties;

    /**
     * @var LockHandler
     */
    private $lockHandler;

    /**
     * @param WorkerInterface          $worker
     * @param VolumeManagerInterface   $volumeManager
     * @param TemplateManagerInterface $templateManager
     * @param MediaClassifier          $mediaClassifier
     * @param Properties               $properties
     * @param string                   $lockDir
     */
    public function __construct(
        WorkerInterface $worker,
        VolumeManagerInterface $volumeManager,
        TemplateManagerInterface $templateManager,
        MediaClassifier $mediaClassifier,
        Properties $properties,
        $lockDir)
    {
        $this->worker = $worker;
        $this->volumeManager = $volumeManager;
        $this->templateManager = $templateManager;
        $this->mediaClassifier = $mediaClassifier;
        $this->properties = $properties;
        $this->lockHandler = new LockHandler('mediacache_lock', $lockDir);
    }

    /**
     * @param Queue    $queue
     * @param callable $callback
     */
    public function processQueue(Queue $queue, callable $callback = null)
    {
        if (!$this->lockHandler->lock(false)) {
            throw new AlreadyRunningException('Another cache worker process running.');
        }

        foreach ($queue->all() as $cacheItem) {
            $this->doProcess($cacheItem, $callback);
        }
        $this->lockHandler->release();
    }

    /**
     * @param CacheItem $cacheItem
     * @param callable  $callback
     *
     * @return CacheItem
     */
    public function processItem(CacheItem $cacheItem, callable $callback = null)
    {
        if (!$this->lockHandler->lock(false)) {
            throw new AlreadyRunningException('Another cache worker process running.');
        }

        $cacheItem = $this->doProcess($cacheItem, $callback);
        $this->lockHandler->release();

        return $cacheItem;
    }

    /**
     * @param CacheItem $cacheItem
     * @param callable  $callback
     *
     * @return CacheItem
     */
    private function doProcess(CacheItem $cacheItem, callable $callback = null)
    {
        $volume = $this->volumeManager->getById($cacheItem->getVolumeId());
        $file = $volume->findFile($cacheItem->getFileId(), $cacheItem->getFileVersion());

        if (!$file) {
            return null;
        }

        $template = $this->templateManager->find($cacheItem->getTemplateKey());

        $mediaType = $this->mediaClassifier->getCollection()->get($file->getMediaType());

        $cacheItem = $this->worker->process($template, $file, $mediaType);

        if ($callback) {
            if (!$cacheItem) {
                call_user_func($callback, 'no_cacheitem', $this->worker, $cacheItem);
            } else {
                call_user_func($callback, $cacheItem->getCacheStatus(), $this->worker, $cacheItem);
            }
        }

        $this->properties->set('mediacache', 'last_run', date('Y-m-d H:i:s'));

        return $cacheItem;
    }
}
