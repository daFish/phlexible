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
     * @var UserManagerInterface
     */
    private $userManager;

    /**
     * @var FolderUsageManager
     */
    private $folderUsageManager;

    /**
     * @param UserManagerInterface $userManager
     * @param FolderUsageManager   $folderUsageManager
     */
    public function __construct(UserManagerInterface $userManager, FolderUsageManager $folderUsageManager)
    {
        $this->userManager = $userManager;
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

        try {
            $createUser = $this->userManager->find($folder->getCreateUserId());
            $createUserName = $createUser->getDisplayName();
        } catch (\Exception $e) {
            $createUserName = 'Unknown';
        }

        try {
            if ($folder->getModifyUserId()) {
                $modifyUser = $this->userManager->find($folder->getModifyUserId());
                $modifyUserName = $modifyUser->getDisplayName();
            } else {
                $modifyUserName = 'Unknown';
            }
        } catch (\Exception $e) {
            $modifyUserName = 'Unknown';
        }

        $usage = $this->folderUsageManager->getStatus($folder);
        $usedIn = $this->folderUsageManager->getUsedIn($folder);

        $attributes = $folder->getAttributes();

        $data = [
            'id'           => $folder->getId(),
            'name'         => $folder->getName(),
            'path'         => '/'. $folder->getPath(),
            'hasVersions'  => $hasVersions,
            'volumeId'     => $volume->getId(),
            'createUser'   => $createUserName,
            'createUserId' => $folder->getCreateUserId(),
            'createTime'   => $folder->getCreatedAt()->format('Y-m-d H:i:s'),
            'modifyUser'   => $modifyUserName,
            'modifyUserId' => $folder->getModifyUserId(),
            'modifyTime'   => $folder->getModifiedAt() ? $folder->getModifiedAt()->format('Y-m-d H:i:s') : null,
            'usedIn'       => $usedIn,
            'used'         => $usage,
            'attributes'   => $attributes,
        ];

        return $data;
    }
}
