<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\MediaManagerBundle\Serializer;

use Phlexible\Bundle\MediaCacheBundle\Entity\CacheItem;
use Phlexible\Bundle\UserBundle\Model\UserManagerInterface;
use Phlexible\Component\MediaCache\Model\CacheManagerInterface;
use Phlexible\Component\MediaManager\Usage\FileUsageManager;
use Phlexible\Component\MediaManager\Volume\ExtendedFileInterface;
use Phlexible\Component\MediaType\Model\MediaTypeManagerInterface;
use Phlexible\Component\Volume\Model\FileInterface;
use Phlexible\Component\Volume\VolumeInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * File serializer
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class FileSerializer
{
    /**
     * @var MediaTypeManagerInterface
     */
    private $mediaTypeManager;

    /**
     * @var CacheManagerInterface
     */
    private $cacheManager;

    /**
     * @var FileUsageManager
     */
    private $fileUsageManager;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @param MediaTypeManagerInterface $mediaTypeManager
     * @param CacheManagerInterface     $cacheManager
     * @param FileUsageManager          $fileUsageManager
     * @param RouterInterface           $router
     */
    public function __construct(
        MediaTypeManagerInterface $mediaTypeManager,
        CacheManagerInterface $cacheManager,
        FileUsageManager $fileUsageManager,
        RouterInterface $router
    )
    {
        $this->mediaTypeManager = $mediaTypeManager;
        $this->cacheManager = $cacheManager;
        $this->fileUsageManager = $fileUsageManager;
        $this->router = $router;
    }

    /**
     * Serialize file
     *
     * @param VolumeInterface $volume
     * @param FileInterface   $file
     * @param string          $language
     * @param array           $fields
     *
     * @return array
     */
    public function serialize(VolumeInterface $volume, FileInterface $file, $language, array $fields = array())
    {
        $all = true;

        $folder = $volume->findFolder($file->getFolderId());
        $hasVersions = $volume->hasFeature('versions');

        $version = 1;
        if ($hasVersions) {
            $version = $file->getVersion();
        }

        $usageStatus = $this->fileUsageManager->getStatus($file);

        $meta = [];
        if ($all || in_array('meta', $fields)) {
            // TODO: enable
            //foreach ($asset->getMetas()->getAll() as $metaData) {
            //    foreach ($metaData->getValues() as $key => $value) {
            //        $meta[$metaData->getTitle()][$key] = $value;
            //    }
            //}
        }

        $cache = [];
        if ($all || in_array('cache', $fields)) {
            $cacheItems = $this->cacheManager->findByFile($file->getID(), $version);
            foreach ($cacheItems as $cacheItem) {
                if ($cacheItem->getCacheStatus() === CacheItem::STATUS_OK) {
                    $cache[$cacheItem->getTemplateKey()] = $this->router->generate(
                        'phlexible_mediamanager_media',
                        [
                            'fileId'      => $file->getId(),
                            'fileVersion' => $file->getVersion(),
                            'templateKey' => $cacheItem->getTemplateKey(),
                        ]
                    );
                } else {
                    $cache[$cacheItem->getTemplateKey()] = $this->router->generate(
                        'phlexible_mediamanager_media_delegate',
                        [
                            'mediaTypeName' => $file->getMediaType(),
                            'templateKey'   => $cacheItem->getTemplateKey(),
                        ]
                    );
                }
            }
        }

        $usedIn = array();
        if ($all || in_array('usedIn', $fields)) {
            $usedIn = $this->fileUsageManager->getUsedIn($file);
        }

        $attributes = array();
        if ($all || in_array('attributes', $fields)) {
            $attributes = $file->getAttributes();
        }

        $versions = array();
        if ($all || in_array('versions', $fields)) {
            foreach ($volume->findFileVersions($file->getId()) as $fileVersion) {
                $version = array(
                    'id'            => $fileVersion->getId(),
                    'folderId'      => $fileVersion->getFolderId(),
                    'name'          => $fileVersion->getName(),
                    'size'          => $fileVersion->getSize(),
                    'version'       => $fileVersion->getVersion(),
                    'mediaType'     => null,
                    'mediaCategory' => null,
                    'createUser'    => $fileVersion->getCreateUser(),
                    'createTime'    => $fileVersion->getCreatedAt()->format('Y-m-d'),
                );

                if ($fileVersion instanceof ExtendedFileInterface) {
                    $version['mediaType'] = $fileVersion->getMediaType();
                    $version['mediaCategory'] = $fileVersion->getMediaCategory();
                }

                $versions[] = $version;
            }
        }

        $navigation = array();
        if ($all || in_array('navigation', $fields)) {
            /*
            $previousFile = $site->findPreviousFile($file, 'name ASC');
            $nextFile = $site->findNextFile($file, 'name ASC');
            */

            if (!empty($previousFile)) {
                $navigation['prevId'] = $previousFile->getId();
                $navigation['prevVersion'] = $previousFile->getVersion();
            }

            if (!empty($nextFile)) {
                $navigation['nextId'] = $nextFile->getId();
                $navigation['nextVersion'] = $nextFile->getVrsion();
            }
        }

        $data = [
            'id'              => $file->getID(),
            'name'            => $file->getName(),
            'path'            => '/' . $folder->getPath() . $file->getName(),
            'volumeId'        => $volume->getId(),
            'folderId'        => $file->getFolderID(),
            'folderPath'      => '/' . $folder->getPath(),
            'hasVersions'     => $hasVersions,
            'mimeType'        => $file->getMimetype(),
            'mediaCategory'   => null,
            'mediaType'       => null,
            'mediaTypeTitle'  => null,
            'present'         => file_exists($volume->getPhysicalPath($file)),
            'size'            => $file->getSize(),
            'hidden'          => $file->isHidden() ? 1 : 0,
            'version'         => $version,
            'usageStatus'     => $usageStatus,
            'createUser'      => $file->getCreateUser(),
            'createTime'      => $file->getCreatedAt()->format('Y-m-d H:i:s'),
            'modifyUser'      => $file->getModifyUser(),
            'modifyTime'      => $file->getModifiedAt() ? $file->getModifiedAt()->format('Y-m-d H:i:s') : null,
            'cache'           => $cache,
            'meta'            => $meta,
            'usedIn'          => $usedIn,
            'attributes'      => $attributes,
            'versions'        => $versions,
            'navigation'      => $navigation,
        ];

        if ($file instanceof ExtendedFileInterface) {
            $mediaType = $this->mediaTypeManager->find(strtolower($file->getMediaType()));
            if (!$mediaType) {
                $mediaType = $this->mediaTypeManager->create();
                $mediaType->setName('unknown');
            }
            $mediaTypeTitle = $mediaType->getTitle($language);

            $data['mediaType'] = $file->getMediaType();
            $data['mediaTypeTitle'] = $mediaTypeTitle;
            $data['mediaCategory'] = $file->getMediaCategory();
        }

        return $data;
    }
}
