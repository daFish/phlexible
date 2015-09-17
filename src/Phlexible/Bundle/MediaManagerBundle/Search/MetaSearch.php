<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\MediaManagerBundle\Search;

use Phlexible\Bundle\SearchBundle\Search\SearchResult;
use Phlexible\Bundle\SearchBundle\SearchProvider\SearchProviderInterface;
use Phlexible\Bundle\UserBundle\Model\UserManagerInterface;
use Phlexible\Component\MediaManager\Meta\FileMetaDataManager;
use Phlexible\Component\MediaManager\Volume\ExtendedFileInterface;
use Phlexible\Component\Volume\Model\VolumeManagerInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Meta search.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class MetaSearch implements SearchProviderInterface
{
    /**
     * @var VolumeManagerInterface
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
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * @param VolumeManagerInterface        $volumeManager
     * @param FileMetaDataManager           $metaDataManager
     * @param UserManagerInterface          $userManager
     * @param AuthorizationCheckerInterface $authorizationChecker
     */
    public function __construct(
        VolumeManagerInterface $volumeManager,
        FileMetaDataManager $metaDataManager,
        UserManagerInterface $userManager,
        AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->volumeManager = $volumeManager;
        $this->metaDataManager = $metaDataManager;
        $this->userManager = $userManager;
        $this->authorizationChecker = $authorizationChecker;
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
        // TODO: repair
        return array();
        $files = array();
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

        $folders = array();
        $results = array();
        foreach ($files as $file) {
            /* @var $file ExtendedFileInterface */

            if (empty($folders[$file->getFolderId()])) {
                $folders[$file->getFolderId()] = $file->getVolume()->findFolder($file->getFolderId());
            }

            if (!$this->authorizationChecker->isGranted('ROLE_SUPER_ADMIN') && !$this->authorizationChecker->isGranted('FILE_READ', $folders[$file->getFolderId()])) {
                continue;
            }

            $folderPath = $folders[$file->getFolderId()]->getIdPath();

            $results[] = new SearchResult(
                $file->getId(),
                $file->getName(),
                $file->getCreateUser(),
                $file->getCreatedAt(),
                '/media/'.$file->getId().'/_mm_small',
                'Mediamanager Meta Search',
                array(
                    'handler' => 'media',
                    'parameters' => array(
                        'startFileId' => $file->getId(),
                        'startFolderPath' => '/'.implode('/', $folderPath),
                    ),
                )
            );
        }

        return $results;
    }
}
