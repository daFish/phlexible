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

use Phlexible\Bundle\UserBundle\Model\UserManagerInterface;
use Phlexible\Component\MediaManager\Usage\FolderUsageManager;
use Phlexible\Component\MediaManager\Volume\ExtendedFolderInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * Folder serializer
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class FolderSerializer
{
    /**
     * @var FolderUsageManager
     */
    private $folderUsageManager;

    /**
     * @param FolderUsageManager $folderUsageManager
     */
    public function __construct(FolderUsageManager $folderUsageManager)
    {
        $this->folderUsageManager = $folderUsageManager;
    }

    /**
     * Serialize file
     *
     * @param ExtendedFolderInterface $folder
     * @param string                  $language
     *
     * @return array
     */
    public function serialize(ExtendedFolderInterface $folder)
    {
        $volume = $folder->getVolume();
        $hasVersions = $volume->hasFeature('versions');

        $usage = $this->folderUsageManager->getStatus($folder);
        $usedIn = $this->folderUsageManager->getUsedIn($folder);

        $attributes = $folder->getAttributes();

        $data = array(
            'id'           => $folder->getId(),
            'name'         => $folder->getName(),
            'path'         => '/'. $folder->getPath(),
            'hasVersions'  => $hasVersions,
            'volumeId'     => $volume->getId(),
            'createUser'   => $folder->getCreateUser(),
            'createTime'   => $folder->getCreatedAt()->format('Y-m-d H:i:s'),
            'modifyUser'   => $folder->getModifyUser(),
            'modifyTime'   => $folder->getModifiedAt() ? $folder->getModifiedAt()->format('Y-m-d H:i:s') : null,
            'usedIn'       => $usedIn,
            'used'         => $usage,
            'attributes'   => $attributes,
        );

        return $data;
    }
}
