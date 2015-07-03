<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\MediaCache\Worker;

use Phlexible\Bundle\MediaCacheBundle\Entity\CacheItem;
use Phlexible\Component\MediaExtractor\Transmutor;
use Phlexible\Component\MediaManager\Volume\ExtendedFileInterface;
use Phlexible\Component\MediaCache\CacheIdStrategy\CacheIdStrategyInterface;
use Phlexible\Component\MediaCache\Model\CacheManagerInterface;
use Phlexible\Component\MediaCache\Storage\StorageManager;
use Phlexible\Component\MediaTemplate\Applier\ImageTemplateApplier;
use Phlexible\Component\MediaTemplate\Model\ImageTemplate;
use Phlexible\Component\MediaTemplate\Model\TemplateInterface;
use Phlexible\Component\MediaType\Model\MediaType;
use Phlexible\Component\MediaType\Model\MediaTypeManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Image cache worker
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ImageWorker extends AbstractWorker
{
    /**
     * @var StorageManager
     */
    private $storageManager;

    /**
     * @var Transmutor
     */
    private $transmutor;

    /**
     * @var CacheManagerInterface
     */
    private $cacheManager;

    /**
     * @var MediaTypeManagerInterface
     */
    private $mediaTypeManager;

    /**
     * @var CacheIdStrategyInterface
     */
    private $cacheIdStrategy;

    /**
     * @var ImageTemplateApplier
     */
    private $applier;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var string
     */
    private $tempDir;

    /**
     * @param StorageManager            $storageManager
     * @param Transmutor                $transmutor
     * @param CacheManagerInterface     $cacheManager
     * @param MediaTypeManagerInterface $mediaTypeManager
     * @param CacheIdStrategyInterface  $cacheIdStrategy
     * @param ImageTemplateApplier      $applier
     * @param LoggerInterface           $logger
     * @param string                    $tempDir
     */
    public function __construct(
        StorageManager $storageManager,
        Transmutor $transmutor,
        CacheManagerInterface $cacheManager,
        MediaTypeManagerInterface $mediaTypeManager,
        CacheIdStrategyInterface $cacheIdStrategy,
        ImageTemplateApplier $applier,
        LoggerInterface $logger,
        $tempDir)
    {
        $this->storageManager = $storageManager;
        $this->transmutor = $transmutor;
        $this->cacheManager = $cacheManager;
        $this->mediaTypeManager = $mediaTypeManager;
        $this->cacheIdStrategy = $cacheIdStrategy;
        $this->applier = $applier;
        $this->logger = $logger;
        $this->tempDir = $tempDir;
    }

    /**
     * {@inheritdoc}
     */
    public function accept(TemplateInterface $template, ExtendedFileInterface $file, MediaType $mediaType)
    {
        return $template instanceof ImageTemplate;
    }

    /**
     * {@inheritdoc}
     */
    public function process(TemplateInterface $template, ExtendedFileInterface $file, MediaType $mediaType)
    {
        $imageFile = $this->transmutor->transmuteToImage($file);

        if ($imageFile !== null && file_exists($imageFile)) {
            // we have a preview image from the asset
            return $this->work($template, $file, $imageFile);
        } elseif (!file_exists($file->getVolume()->getPhysicalPath($file))) {
            // file is completely missing
            return $this->work($template, $file, $file->getVolume()->getPhysicalPath($file), true);
        } elseif ($imageFile === null) {
            return $this->work($template, $file);
        }

        return null;
    }

    /**
     * Apply template to filename
     *
     * @param ImageTemplate         $template
     * @param ExtendedFileInterface $file
     * @param string                $inputFilename
     * @param bool                  $missing
     *
     * @return CacheItem
     */
    private function work(ImageTemplate $template, ExtendedFileInterface $file, $inputFilename = null, $missing = false)
    {
        $cacheFilename = null;

        $volume = $file->getVolume();
        $fileId = $file->getId();
        $fileVersion = $file->getVersion();

        $cacheId = $this->cacheIdStrategy->createCacheId($template, $file);
        $tempFilename = $this->tempDir . '/' . $cacheId . '.' . $template->getParameter('format');

        $pathinfo = pathinfo($file->getVolume()->getPhysicalPath($file));

        $cacheItem = $this->cacheManager->findOneBy(array(
            'templateKey' => $template->getKey(),
            'fileId' => $fileId,
            'fileVersion' => $fileVersion
        ));
        if (!$cacheItem) {
            $cacheItem = new CacheItem();
        }

        $cacheItem
            ->setId($cacheId)
            ->setVolumeId($volume->getId())
            ->setFileId($fileId)
            ->setFileVersion($fileVersion)
            ->setTemplateKey($template->getKey())
            ->setTemplateRevision($template->getRevision())
            ->setCacheStatus(CacheItem::STATUS_DELEGATE)
            ->setQueueStatus(CacheItem::QUEUE_DONE)
            ->setMimeType($file->getMimeType())
            ->setMediaType(strtolower($file->getMediaType()))
            ->setExtension(isset($pathinfo['extension']) ? $pathinfo['extension'] : '')
            ->setFileSize(0)
            ->setError(null);

        if ($missing) {
            $this->applyError(
                $cacheItem,
                CacheItem::STATUS_MISSING,
                'Input file not found.',
                $inputFilename,
                $template,
                $file
            );
        } elseif ($inputFilename === null) {
            $this->applyError(
                $cacheItem,
                CacheItem::STATUS_DELEGATE,
                'No preview image.',
                $inputFilename,
                $template,
                $file
            );
        } elseif (!$this->applier->isAvailable($inputFilename)) {
            $this->applyError(
                $cacheItem,
                CacheItem::STATUS_MISSING,
                'No suitable image template applier found.',
                $inputFilename,
                $template,
                $file
            );
        } else {
            $filesystem = new Filesystem();
            if (!$filesystem->exists($this->tempDir)) {
                $filesystem->mkdir($this->tempDir, 0777);
            }
            if (!$filesystem->exists($tempFilename)) {
                $filesystem->remove($tempFilename);
            }

            try {
                $image = $this->applier->apply($template, $file, $inputFilename, $tempFilename);

                $filesystem->chmod($tempFilename, 0777);

                $mediaType = $this->mediaTypeManager->findByFilename($tempFilename);

                $cacheItem
                    ->setCacheStatus(CacheItem::STATUS_OK)
                    ->setQueueStatus(CacheItem::QUEUE_DONE)
                    ->setMimeType($mediaType->getMimetype())
                    ->setMediaType($mediaType->getName())
                    ->setExtension(pathinfo($tempFilename, PATHINFO_EXTENSION))
                    ->setFilesize(filesize($tempFilename))
                    ->setWidth($image->getSize()->getWidth())
                    ->setHeight($image->getSize()->getHeight())
                    ->setFinishedAt(new \DateTime());
            } catch (\Exception $e) {
                $cacheItem
                    ->setCacheStatus(CacheItem::STATUS_ERROR)
                    ->setQueueStatus(CacheItem::QUEUE_ERROR)
                    ->setError($e)
                    ->setFinishedAt(new \DateTime());
            }

            if ($cacheItem->getCacheStatus() === CacheItem::STATUS_OK) {
                $storage = $this->storageManager->get($template->getStorage());
                $storage->store($cacheItem, $tempFilename);
            }
        }

        $this->cacheManager->updateCacheItem($cacheItem);

        if ($cacheItem->getError()) {
            $this->logger->error($cacheItem->getError());
        }

        return $cacheItem;
    }
}
