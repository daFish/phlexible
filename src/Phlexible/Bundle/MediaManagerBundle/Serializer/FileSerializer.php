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
    public function serialize(ExtendedFileInterface $file, $language, array $fields = array())
    {
        $all = in_array('all', $fields);

        $volume = $file->getVolume();
        $folder = $volume->findFolder($file->getFolderId());
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
            'mediaCategory'   => $file->getMediaCategory(),
            'mediaType'       => $file->getMediaType(),
            'mediaTypeTitle'  => $mediaTypeTitle,
            'present'         => file_exists($file->getPhysicalPath()),
            'size'            => $file->getSize(),
            'hidden'          => $file->isHidden() ? 1 : 0,
            'version'         => $version,
            'usageStatus'     => $usageStatus,
            'createUser'      => $createUserName,
            'createUserId'    => $file->getCreateUserId(),
            'createTime'      => $file->getCreatedAt()->format('Y-m-d H:i:s'),
            'modifyUser'      => $modifyUserName,
            'modifyUserId'    => $file->getModifyUserId(),
            'modifyTime'      => $file->getModifiedAt() ? $file->getModifiedAt()->format('Y-m-d H:i:s') : null,
            'cache'           => $cache,
            'meta'            => $meta,
            'usedIn'          => $usedIn,
            'attributes'      => $attributes,
            'versions'        => $versions,
            'navigation'      => $navigation,
        ];

        return $data;
    }
}
