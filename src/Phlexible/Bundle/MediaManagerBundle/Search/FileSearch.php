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
use Phlexible\Component\MediaManager\Volume\ExtendedFileInterface;
use Phlexible\Component\Volume\Model\VolumeManagerInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Webmozart\Expression\Comparison\Contains;
use Webmozart\Expression\Logic\Disjunction;
use Webmozart\Expression\Selector\Key;

/**
 * File search.
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
        $expr = new Disjunction(array(
            new Key('name', new Contains($query)),
        ));

        $files = array();
        foreach ($this->volumeManager->all() as $volume) {
            $foundFiles = $volume->findFilesByExpression($expr);
            if ($foundFiles) {
                $files += $foundFiles;
            }
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
                'Mediamanager File Search',
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
