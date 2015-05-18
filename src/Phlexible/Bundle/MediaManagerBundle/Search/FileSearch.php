<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaManagerBundle\Search;

use Phlexible\Component\MediaManager\Volume\ExtendedFileInterface;
use Phlexible\Bundle\SearchBundle\Search\SearchResult;
use Phlexible\Bundle\SearchBundle\SearchProvider\SearchProviderInterface;
use Phlexible\Bundle\UserBundle\Model\UserManagerInterface;
use Phlexible\Component\Volume\Model\VolumeManagerInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * File search
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class FileSearch implements SearchProviderInterface
{
    /**
     * @var VolumeManagerInterface
     */
    private $volumeManager;

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
     * @param UserManagerInterface          $userManager
     * @param AuthorizationCheckerInterface $authorizationChecker
     */
    public function __construct(
        VolumeManagerInterface $volumeManager,
        UserManagerInterface $userManager,
        AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->volumeManager = $volumeManager;
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
        $files = [];
        foreach ($this->volumeManager->all() as $volume) {
            $foundFiles = $volume->search($query);
            if ($foundFiles) {
                $files += $foundFiles;
            }
        }

        $folders = [];

        $results = [];
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
                '/media/' . $file->getId() . '/_mm_small',
                'Mediamanager File Search',
                [
                    'handler'    => 'media',
                    'parameters' => [
                        'startFileId'     => $file->getId(),
                        'startFolderPath' => '/' . implode('/', $folderPath)
                    ],
                ]
            );
        }

        return $results;
    }
}
