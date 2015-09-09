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

use Phlexible\Component\MediaManager\Usage\FolderUsageManager;
use Phlexible\Component\MediaManager\Volume\ExtendedFolderInterface;

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
     *
     * @return array
     */
    public function serialize(ExtendedFolderInterface $folder)
    {
        $volume = $folder->getVolume();

        $usage = $this->folderUsageManager->getStatus($folder);
        $usedIn = $this->folderUsageManager->getUsedIn($folder);

        $data = array(
            'id'          => $folder->getId(),
            'name'        => $folder->getName(),
            'path'        => $folder->getPath(),
            'volumeId'    => $folder->getVolumeId(),
            'createdBy'   => $folder->getCreateUser(),
            'createdAt'   => $folder->getCreatedAt()->format('Y-m-d H:i:s'),
            'modifiedBy'  => $folder->getModifyUser(),
            'modifiedAt'  => $folder->getModifiedAt() ? $folder->getModifiedAt()->format('Y-m-d H:i:s') : null,
            'attributes'  => $folder->getAttributes(),
            'usedIn'      => $usedIn,
            'used'        => $usage,
        );

        return $data;
    }
}
