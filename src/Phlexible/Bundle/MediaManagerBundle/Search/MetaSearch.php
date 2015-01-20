<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaManagerBundle\Search;

use Phlexible\Bundle\MediaManagerBundle\Meta\FileMetaDataManager;
use Phlexible\Bundle\MediaManagerBundle\Volume\ExtendedFileInterface;
use Phlexible\Bundle\SearchBundle\Search\SearchResult;
use Phlexible\Bundle\SearchBundle\SearchProvider\SearchProviderInterface;
use Phlexible\Bundle\UserBundle\Model\UserManagerInterface;
use Phlexible\Component\Volume\VolumeManager;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * Meta search
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class MetaSearch implements SearchProviderInterface
{
    /**
     * @var VolumeManager
     */
    private $volumeManager;

    /**
     * @var FileMetaDataManager
     */
    private $metaDataManager;

    /**
     * @var UserManagerInterface
     */
    private $userManager;

    /**
     * @var SecurityContextInterface
     */
    private $securityContext;

    /**
     * @param VolumeManager            $volumeManager
     * @param FileMetaDataManager      $metaDataManager
     * @param UserManagerInterface     $userManager
     * @param SecurityContextInterface $securityContext
     */
    public function __construct(VolumeManager $volumeManager, FileMetaDataManager $metaDataManager, UserManagerInterface $userManager, SecurityContextInterface $securityContext)
    {
        $this->volumeManager = $volumeManager;
        $this->metaDataManager = $metaDataManager;
        $this->userManager = $userManager;
        $this->securityContext = $securityContext;
    }

    /**
     * {@inheritdoc}
     */
    public function getRole()
    {
        return 'ROLE_MEDIA';
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchKey()
    {
        return 'mm';
    }

    /**
     * {@inheritdoc}
     */
    public function search($query)
    {
        $files = [];
        foreach ($this->volumeManager->all() as $volume) {
            $foundFiles = $volume->search($query);
            if ($foundFiles) {
                $files += $foundFiles;
            }
        }

        foreach ($this->metaDataManager->findByValue($query) as $metaData) {
            $identifiers = $metaData->getIdentifiers();
            $file = $volume->findFile($identifiers['file_id']);
            $files[$file->getId()] = $file;
        }

        $folders = [];
        $results = [];
        foreach ($files as $file) {
            /* @var $file ExtendedFileInterface */

            if (empty($folders[$file->getFolderId()])) {
                $folders[$file->getFolderId()] = $file->getVolume()->findFolder($file->getFolderId());
            }

            if (!$this->securityContext->isGranted($folders[$file->getFolderId()], 'FILE_READ')) {
                continue;
            }

            $folderPath = $folders[$file->getFolderId()]->getIdPath();

            try {
                $createUser = $this->userManager->find($file->getCreateUserId());
            } catch (\Exception $e) {
                $createUser = $this->userManager->getSystemUser();
            }

            $results[] = new SearchResult(
                $file->getId(),
                $file->getName(),
                $createUser->getDisplayname(),
                $file->getCreatedAt()->format('U'),
                '/media/' . $file->getId() . '/_mm_small',
                'Mediamanager Meta Search',
                [
                    'xtype'      => 'Phlexible.mediamanager.menuhandle.MediaHandle',
                    'parameters' => [
                        'startFile_id'    => $file->getId(),
                        'startFolderPath' => $folderPath
                    ],
                ]
            );
        }

        return $results;
    }
}
