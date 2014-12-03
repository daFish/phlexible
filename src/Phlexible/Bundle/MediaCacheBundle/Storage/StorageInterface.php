<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaCacheBundle\Storage;

use Phlexible\Bundle\MediaCacheBundle\Entity\CacheItem;
use Phlexible\Bundle\MediaManagerBundle\Volume\ExtendedFileInterface;

/**
 * Storage interface
 *
 * @author Peter Fahsel <pfahsel@brainbits.net>
 */
interface StorageInterface
{
    /**
     * @param CacheItem $cacheItem
     * @param string    $filename
     */
    public function store(CacheItem $cacheItem, $filename);

    /**
     * @param ExtendedFileInterface $file
     * @param string                $baseUrl
     *
     * @return array
     */
    public function getUrls(ExtendedFileInterface $file, $baseUrl);

    /**
     * @param ExtendedFileInterface $file
     * @param CacheItem             $cacheItem
     * @param string                $baseUrl
     *
     * @return array
     */
    public function getCacheUrls(ExtendedFileInterface $file, CacheItem $cacheItem, $baseUrl);

    /**
     * @param CacheItem $cacheItem
     *
     * @return string
     */
    public function getLocalPath(CacheItem $cacheItem);

    /**
     * @param string $fileId
     * @param string $fileName
     */
    public function deleteByFileId($fileId, $fileName);
}
