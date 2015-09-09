<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\MediaCache\Worker;

use Phlexible\Bundle\MediaCacheBundle\Entity\CacheItem;
use Phlexible\Component\MediaCache\CacheIdStrategy\CacheIdStrategyInterface;
use Phlexible\Component\MediaCache\Model\CacheManagerInterface;
use Phlexible\Component\MediaCache\Specifier\SpecifierInterface;
use Phlexible\Component\MediaCache\Specifier\SpecifierResolver;
use Phlexible\Component\MediaCache\Storage\StorageManager;
use Phlexible\Component\MediaManager\Volume\ExtendedFileInterface;
use Phlexible\Component\MediaTemplate\Model\TemplateInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;
use Temp\MediaClassifier\MediaClassifier;
use Temp\MediaClassifier\Model\MediaType;
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
     * @var SpecifierInterface
     */
    private $specifier;

    /**
     * @var StorageManager
     */
    private $storageManager;

    /**
     * @var CacheManagerInterface
     */
    private $cacheManager;

    /**
     * @var MediaClassifier
     */
    private $mediaClassifier;

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
     * @param Transmuter               $transmuter
     * @param SpecifierInterface       $specifier
     * @param StorageManager           $storageManager
     * @param CacheManagerInterface    $cacheManager
     * @param MediaClassifier          $mediaClassifier
     * @param CacheIdStrategyInterface $cacheIdStrategy
     * @param LoggerInterface          $logger
     * @param string                   $tempDir
     */
    public function __construct(
        Transmuter $transmuter,
        SpecifierInterface $specifier,
        StorageManager $storageManager,
        CacheManagerInterface $cacheManager,
        MediaClassifier $mediaClassifier,
        CacheIdStrategyInterface $cacheIdStrategy,
        LoggerInterface $logger,
        $tempDir)
    {
        $this->transmuter = $transmuter;
        $this->specifier = $specifier;
        $this->storageManager = $storageManager;
        $this->cacheManager = $cacheManager;
        $this->mediaClassifier = $mediaClassifier;
        $this->cacheIdStrategy = $cacheIdStrategy;
        $this->logger = $logger;
        $this->tempDir = $tempDir;
    }

    /**
     * {@inheritdoc}
     */
    public function process(TemplateInterface $template, ExtendedFileInterface $file, MediaType $mediaType)
    {
        $cacheId = $this->cacheIdStrategy->createCacheId($template, $file);

        $cacheItem = $this->cacheManager->find($cacheId);
        if (!$cacheItem) {
            $cacheItem = new CacheItem();
            $cacheItem->setId($cacheId);
        }

        $cacheItem
            ->setVolumeId($file->getVolume()->getId())
            ->setFileId($file->getId())
            ->setFileVersion($file->getVersion())
            ->setTemplateKey($template->getKey())
            ->setTemplateRevision($template->getRevision())
            ->setCacheStatus(CacheItem::STATUS_DELEGATE)
            ->setQueueStatus(CacheItem::QUEUE_DONE)
            ->setMimeType($file->getMimeType())
            ->setMediaType($file->getMediaType())
            ->setExtension('')
            ->setFileSize(0)
            ->setError(null);

        if (!file_exists($file->getVolume()->getPhysicalPath($file))) {
            return $this->applyError(
                $cacheItem,
                CacheItem::STATUS_MISSING,
                'Input file not found.',
                $file->getPhysicalPath(),
                $template,
                $file
            );
        }

        if (!$this->specifier->accept($template)) {
            return $this->applyError(
                $cacheItem,
                CacheItem::STATUS_MISSING,
                'No suitable template specifier found.',
                $file->getPhysicalPath(),
                $template,
                $file
            );
        }

        $tempFilename = $this->tempDir . '/' . $cacheId . '.' . $this->specifier->getExtension($template);

        $filesystem = new Filesystem();
        if (!$filesystem->exists($this->tempDir)) {
            $filesystem->mkdir($this->tempDir, 0777);
        }
        if ($filesystem->exists($tempFilename)) {
            $filesystem->remove($tempFilename);
        }

        try {
            $spec = $this->specifier->specify($template);
            $tempFilename = $this->transmuter->transmute($file->getVolume()->getPhysicalPath($file), $spec, $tempFilename);

            if (!$tempFilename) {
                return $this->applyError(
                    $cacheItem,
                    CacheItem::STATUS_INAPPLICABLE,
                    'File type '.((string) $mediaType).' not convertable to ' . $template->getType() . ' template ' . $template->getKey() . '.',
                    $file->getPhysicalPath(),
                    $template,
                    $file
                );
            }

            $filesystem->chmod($tempFilename, 0777);

            $xfile = new File($tempFilename);
            $mediaType = $this->mediaClassifier->classify($tempFilename);

            $cacheItem
                ->setCacheStatus(CacheItem::STATUS_OK)
                ->setQueueStatus(CacheItem::QUEUE_DONE)
                ->setMimeType($xfile->getMimeType())
                ->setMediaType((string) $mediaType)
                ->setExtension($xfile->getExtension())
                ->setFilesize($xfile->getSize())
                ->setFinishedAt(new \DateTime());
        } catch (\Exception $e) {
            $cacheItem
                ->setCacheStatus(CacheItem::STATUS_ERROR)
                ->setQueueStatus(CacheItem::QUEUE_ERROR)
                ->setError($e)
                ->setFinishedAt(new \DateTime());

            $this->logger->error($e);
        }

        if ($cacheItem->getCacheStatus() === CacheItem::STATUS_OK) {
            $storage = $this->storageManager->get($template->getStorage());
            $storage->store($cacheItem, $tempFilename);
        }

        $this->cacheManager->updateCacheItem($cacheItem);

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
     *
     * @return CacheItem
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
            . 'File mime type: ' . $file->getMimeType() . PHP_EOL
            . 'File media type: ' . strtolower($file->getMediaType());

        $cacheItem
            ->setCacheStatus($status)
            ->setError($error)
            ->setFinishedAt(new \DateTime());

        $this->logger->error($message);

        $this->cacheManager->updateCacheItem($cacheItem);

        return $cacheItem;
    }
}
