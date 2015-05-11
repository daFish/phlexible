<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
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
    public function serialize(ExtendedFolderInterface $folder, $language)
    {
        $volume = $folder->getVolume();
        $hasVersions = $volume->hasFeature('versions');

        $usage = $this->folderUsageManager->getStatus($folder);
        $usedIn = $this->folderUsageManager->getUsedIn($folder);

        $attributes = $folder->getAttributes();

        $data = [
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
        ];

        return $data;
    }
}
