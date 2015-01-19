<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaManagerBundle\Serializer;

use Phlexible\Bundle\MediaCacheBundle\Entity\CacheItem;
use Phlexible\Bundle\MediaManagerBundle\Usage\FileUsageManager;
use Phlexible\Bundle\MediaManagerBundle\Volume\ExtendedFileInterface;
use Phlexible\Bundle\UserBundle\Model\UserManagerInterface;
use Phlexible\Component\MediaCache\Model\CacheManagerInterface;
use Phlexible\Component\MediaType\Model\MediaTypeManagerInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * File serializer
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class FileSerializer
{
    /**
     * @var UserManagerInterface
     */
    private $userManager;

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
     * @param UserManagerInterface      $userManager
     * @param MediaTypeManagerInterface $mediaTypeManager
     * @param CacheManagerInterface     $cacheManager
     * @param FileUsageManager          $fileUsageManager
     * @param RouterInterface           $router
     */
    public function __construct(
        UserManagerInterface $userManager,
        MediaTypeManagerInterface $mediaTypeManager,
        CacheManagerInterface $cacheManager,
        FileUsageManager $fileUsageManager,
        RouterInterface $router
    )
    {
        $this->userManager = $userManager;
        $this->mediaTypeManager = $mediaTypeManager;
        $this->cacheManager = $cacheManager;
        $this->fileUsageManager = $fileUsageManager;
        $this->router = $router;
    }

    /**
     * Serialize file
     *
     * @param ExtendedFileInterface $file
     * @param string                $language
     *
     * @return array
     */
    public function serialize(ExtendedFileInterface $file, $language)
    {
        $volume = $file->getVolume();
        $hasVersions = $volume->hasFeature('versions');

        try {
            $createUser = $this->userManager->find($file->getCreateUserId());
            $createUserName = $createUser->getDisplayName();
        } catch (\Exception $e) {
            $createUserName = 'Unknown';
        }

        try {
            if ($file->getModifyUserId()) {
                $modifyUser = $this->userManager->find($file->getModifyUserId());
                $modifyUserName = $modifyUser->getDisplayName();
            } else {
                $modifyUserName = 'Unknown';
            }
        } catch (\Exception $e) {
            $modifyUserName = 'Unknown';
        }

        $meta = [];
        // TODO: enable
        //foreach ($asset->getMetas()->getAll() as $metaData) {
        //    foreach ($metaData->getValues() as $key => $value) {
        //        $meta[$metaData->getTitle()][$key] = $value;
        //    }
        //}

        $mediaType = $this->mediaTypeManager->find(strtolower($file->getMediaType()));

        if (!$mediaType) {
            $mediaType = $this->mediaTypeManager->create();
            $mediaType->setName('unknown');
        }

        $mediaTypeTitle = $mediaType->getTitle($language);

        $version = 1;
        if ($hasVersions) {
            $version = $file->getVersion();
        }

        $cacheItems = $this->cacheManager->findByFile($file->getID(), $version);
        $cache = [];
        foreach ($cacheItems as $cacheItem) {
            if ($cacheItem->getCacheStatus() === CacheItem::STATUS_OK) {
                $cache[$cacheItem->getTemplateKey()] = $this->router->generate('mediamanager_media', [
                    'file_id'      => $file->getId(),
                    'file_version' => $file->getVersion(),
                    'template_key' => $cacheItem->getTemplateKey(),
                ]);
            } else {
                $cache[$cacheItem->getTemplateKey()] = $this->router->generate('mediamanager_media_delegate', [
                    'mediaTypeName' => $file->getMediaType(),
                    'templateKey'   => $cacheItem->getTemplateKey(),
                ]);
            }
        }

        $usage = $this->fileUsageManager->getStatus($file);
        $usedIn = $this->fileUsageManager->getUsedIn($file);

        $focal = 0;
        if ($file->getAttribute('focalpoint')) {
            $focal = 1;
        }

        $attributes = $file->getAttributes();

        $versions = array();
        foreach ($volume->findFileVersions($file->getId()) as $fileVersion) {
            $versions[] = [
                'id'              => $fileVersion->getId(),
                'folderId'        => $fileVersion->getFolderId(),
                'name'            => $fileVersion->getName(),
                'size'            => $fileVersion->getSize(),
                'version'         => $fileVersion->getVersion(),
                'documentTypeKey' => $fileVersion->getMediaType(),
                'assetType'       => $fileVersion->getMediaCategory(),
                'createUserId'    => $fileVersion->getCreateUserId(),
                'createTime'      => $fileVersion->getCreatedAt()->format('Y-m-d'),
            ];
        }

        /*
        $previousFile = $site->findPreviousFile($file, 'name ASC');
        $nextFile = $site->findNextFile($file, 'name ASC');
        */

        $prevId = null;
        $prevVersion = null;
        if (!empty($previousFile)) {
            $prevId = $previousFile->getId();
            $prevVersion = $previousFile->getVersion();
        }

        $nextId = null;
        $nextVersion = null;
        if (!empty($nextFile)) {
            $nextId = $nextFile->getId();
            $nextVersion = $nextFile->getVrsion();
        }

        $folder = $volume->findFolder($file->getFolderId());
        $data = [
            'id'              => $file->getID(),
            'name'            => $file->getName(),
            'path'            => '/' . $folder->getPath() . $file->getName(),
            'volumeId'        => $volume->getId(),
            'folderId'        => $file->getFolderID(),
            'folderPath'      => '/' . $folder->getPath(),
            'hasVersions'     => $hasVersions,
            'assetType'       => strtolower($file->getMediaCategory()),
            'mimeType'        => $file->getMimetype(),
            'documentType'    => $mediaTypeTitle,
            'documentTypeKey' => strtolower($file->getMediaType()),
            'present'         => file_exists($file->getPhysicalPath()),
            'size'            => $file->getSize(),
            'hidden'          => $file->isHidden() ? 1 : 0,
            'version'         => $version,
            'createUser'      => $createUserName,
            'createUserId'    => $file->getCreateUserId(),
            'createTime'      => $file->getCreatedAt()->format('Y-m-d H:i:s'),
            'modifyUser'      => $modifyUserName,
            'modifyUserId'    => $file->getModifyUserId(),
            'modifyTime'      => $file->getModifiedAt() ? $file->getModifiedAt()->format('Y-m-d H:i:s') : null,
            'cache'           => $cache,
            'meta'            => $meta,
            'usedIn'          => $usedIn,
            'used'            => $usage,
            'focal'           => $focal,
            'attributes'      => $attributes,
            'versions'        => $versions,
            'nextId'          => $nextId,
            'nextVersion'     => $nextVersion,
            'prevId'          => $prevId,
            'prevVersion'     => $prevVersion,
        ];

        return $data;
    }
}
