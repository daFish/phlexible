<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaManagerBundle\Search;

use Phlexible\Bundle\MediaSiteBundle\Model\FileInterface;
use Phlexible\Bundle\MediaSiteBundle\Site\SiteManager;
use Phlexible\Bundle\SearchBundle\Search\SearchResult;
use Phlexible\Bundle\SearchBundle\SearchProvider\SearchProviderInterface;
use Phlexible\Bundle\UserBundle\Model\UserManagerInterface;
use Phlexible\Component\Database\ConnectionManager;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * File search
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class FileSearch implements SearchProviderInterface
{
    /**
     * @var SiteManager
     */
    private $siteManager;

    /**
     * @var UserManagerInterface
     */
    private $userManager;

    /**
     * @var SecurityContextInterface
     */
    private $securityContext;

    /**
     * @param SiteManager              $siteManager
     * @param UserManagerInterface     $userManager
     * @param SecurityContextInterface $securityContext
     */
    public function __construct(SiteManager $siteManager, UserManagerInterface $userManager, SecurityContextInterface $securityContext)
    {
        $this->siteManager = $siteManager;
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
        foreach ($this->siteManager->getAll() as $site) {
            $foundFiles = $site->search($query);
            if ($foundFiles) {
                $files += $foundFiles;
            }
        }

        $folders = [];

        $results = [];
        foreach ($files as $file) {
            /* @var $file FileInterface */

            if (empty($folders[$file->getFolderId()])) {
                $folders[$file->getFolderId()] = $file->getSite()->findFolder($file->getFolderId());
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
                'Mediamanager File Search',
                [
                    'xtype'      => 'Phlexible.mediamanager.menuhandle.MediaHandle',
                    'parameters' => [
                        'start_file_id'     => $file->getId(),
                        'start_folder_path' => $folderPath
                    ],
                ]
            );
        }

        return $results;
    }
}
