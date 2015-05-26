<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\MediaCache\Worker;

use Phlexible\Bundle\MediaCacheBundle\Entity\CacheItem;
use Phlexible\Component\MediaManager\Volume\ExtendedFileInterface;
use Phlexible\Component\MediaCache\CacheIdStrategy\CacheIdStrategyInterface;
use Phlexible\Component\MediaCache\Model\CacheManagerInterface;
use Phlexible\Component\MediaCache\Storage\StorageManager;
use Phlexible\Component\MediaTemplate\Applier\AudioTemplateApplier;
use Phlexible\Component\MediaTemplate\Model\AudioTemplate;
use Phlexible\Component\MediaTemplate\Model\TemplateInterface;
use Phlexible\Component\MediaType\Model\MediaType;
use Phlexible\Component\MediaType\Model\MediaTypeManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Temp\MediaConverter\Transmuter;

/**
 * Cache worker
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class Worker implements WorkerInterface
{
    /**
     * @var Transmuter
     */
    private $transmuter;

    /**
     * @var SpecifierResolver
     */
    private $specifierResolver;

    /**
     * @var StorageManager
     */
    private $storageManager;

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
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var string
     */
    private $tempDir;

    /**
     * @param Transmuter                $transmuter
     * @param SpecifierResolver         $specifierResolver
     * @param StorageManager            $storageManager
     * @param CacheManagerInterface     $cacheManager
     * @param MediaTypeManagerInterface $mediaTypeManager
     * @param CacheIdStrategyInterface  $cacheIdStrategy
     * @param LoggerInterface           $logger
     * @param string                    $tempDir
     */
    public function __construct(
        Transmuter $transmuter,
        SpecifierResolver $specifierResolver,
        StorageManager $storageManager,
        CacheManagerInterface $cacheManager,
        MediaTypeManagerInterface $mediaTypeManager,
        CacheIdStrategyInterface $cacheIdStrategy,
        LoggerInterface $logger,
        $tempDir)
    {
        $this->transmuter = $transmuter;
        $this->specifierResolver = $specifierResolver;
        $this->storageManager = $storageManager;
        $this->cacheManager = $cacheManager;
        $this->mediaTypeManager = $mediaTypeManager;
        $this->cacheIdStrategy = $cacheIdStrategy;
        $this->logger = $logger;
        $this->tempDir = $tempDir;
    }

    /**
     * {@inheritdoc}
     */
    public function process(TemplateInterface $template, ExtendedFileInterface $file, MediaType $mediaType)
    {
        $volume = $file->getVolume();
        $fileId = $file->getId();
        $fileVersion = $file->getVersion();

        $cacheId = $this->cacheIdStrategy->createCacheId($template, $file);
        $tempFilename = $this->tempDir . '/' . $cacheId . '.' . $template->getParameter('audio_format');

        $cacheItem = $this->cacheManager->find($cacheId);
        if (!$cacheItem) {
            $cacheItem = new CacheItem();
            $cacheItem->setId($cacheId);
        }

        $cacheItem
            ->setVolumeId($volume->getId())
            ->setFileId($fileId)
            ->setFileVersion($fileVersion)
            ->setTemplateKey($template->getKey())
            ->setTemplateRevision($template->getRevision())
            ->setCacheStatus(CacheItem::STATUS_DELEGATE)
            ->setQueueStatus(CacheItem::QUEUE_DONE)
            ->setMimeType($file->getMimeType())
            ->setMediaType(strtolower($file->getMediaType()))
            ->setExtension('')
            ->setFileSize(0)
            ->setError(null);

        $spec = $this->specifierResolver->resolve($template, $file, $mediaType);

        if (!file_exists($file->getPhysicalPath())) {
            $this->applyError(
                $cacheItem,
                CacheItem::STATUS_MISSING,
                'Input file not found.',
                $file->getPhysicalPath(),
                $template,
                $file
            );
        } elseif (!$spec) {
            $this->applyError(
                $cacheItem,
                CacheItem::STATUS_MISSING,
                'No suitable template converter found.',
                $file->getPhysicalPath(),
                $template,
                $file
            );
        } else {
            $filesystem = new Filesystem();
            if (!$filesystem->exists($this->tempDir)) {
                $filesystem->mkdir($this->tempDir, 0777);
            }
            if ($filesystem->exists($tempFilename)) {
                $filesystem->remove($tempFilename);
            }

            try {
                $spec = $this->specifierResolver->resolve($template, $file, $mediaType);
                $this->transmuter->transmute($file->getPhysicalPath(), $spec, $tempFilename);

                $filesystem->chmod($tempFilename, 0777);

                $mediaType = $this->mediaTypeManager->findByFilename($tempFilename);

                $cacheItem
                    ->setCacheStatus(CacheItem::STATUS_OK)
                    ->setQueueStatus(CacheItem::QUEUE_DONE)
                    ->setMimeType($mediaType->getMimetype())
                    ->setMediaType($mediaType->getName())
                    ->setExtension(pathinfo($tempFilename, PATHINFO_EXTENSION))
                    ->setFilesize(filesize($tempFilename))
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

    /**
     * Apply error to cache item
     *
     * @param CacheItem             $cacheItem
     * @param string                $status
     * @param string                $message
     * @param string                $inputFilename
     * @param TemplateInterface     $template
     * @param ExtendedFileInterface $file
     */
    protected function applyError(
        CacheItem $cacheItem,
        $status,
        $message,
        $inputFilename,
        TemplateInterface $template,
        ExtendedFileInterface $file)
    {
        $error = $message . PHP_EOL
            . PHP_EOL
            . 'Template type: ' . $template->getType() . PHP_EOL
            . 'Template key: ' . $template->getKey() . PHP_EOL
            . 'File name: ' . $file->getName() . PHP_EOL
            . 'File path: ' . $inputFilename . PHP_EOL
            . 'File ID: ' . $file->getId() . ':' . $file->getVersion() . PHP_EOL
            . 'File type: ' . $file->getMimeType() . PHP_EOL
            . 'File media type: ' . strtolower($file->getMediaType());

        $cacheItem
            ->setCacheStatus($status)
            ->setError($error);
    }
}
