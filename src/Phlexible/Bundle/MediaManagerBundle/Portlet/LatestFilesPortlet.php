<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\MediaManagerBundle\Portlet;

use Phlexible\Bundle\DashboardBundle\Portlet\Portlet;
use Phlexible\Component\MediaCache\Model\CacheManagerInterface;
use Phlexible\Component\Volume\Model\VolumeManagerInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Latest files portlet
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class LatestFilesPortlet extends Portlet
{
    /**
     * @var VolumeManagerInterface
     */
    private $volumeManager;

    /**
     * @var CacheManagerInterface
     */
    private $cacheManager;

    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * @var string
     */
    private $style;

    /**
     * @var int
     */
    private $numItems;

    /**
     * @param VolumeManagerInterface        $volumeManager
     * @param CacheManagerInterface         $cacheManager
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param string                        $style
     * @param int                           $numItems
     */
    public function __construct(
        VolumeManagerInterface $volumeManager,
        CacheManagerInterface $cacheManager,
        AuthorizationCheckerInterface $authorizationChecker,
        $style,
        $numItems)
    {
        $this->volumeManager = $volumeManager;
        $this->cacheManager = $cacheManager;
        $this->authorizationChecker = $authorizationChecker;
        $this->style = $style;
        $this->numItems = $numItems;
    }

    /**
     * Return settings
     *
     * @return array
     */
    public function getSettings()
    {
        return [
            'style' => $this->style
        ];
    }

    /**
     * Return Portlet data
     *
     * @return array
     */
    public function getData()
    {
        $data = [];

        try {
            $files = $this->volumeManager->findFilesBy(array(), array('createdAt' => 'DESC'), 10);

            foreach ($files as $file) {
                $folder = $this->volumeManager->findFolder($file->getFolderId());

                if (!$this->authorizationChecker->isGranted('ROLE_SUPER_ADMIN') && !$this->authorizationChecker->isGranted('FILE_READ', $folder)) {
                    continue;
                }

                $cacheItems = $this->cacheManager->findByFile($file->getId(), $file->getVersion());
                $cacheStatus = [];
                foreach ($cacheItems as $cacheItem) {
                    $cacheStatus[$cacheItem->getTemplateKey()] =
                        $cacheItem->getCacheStatus() . ';' . $cacheItem->getCreatedAt()->format('YmdHis');
                }

                $data[] = [
                    'id'          => sprintf('%s___%s', $file->getId(), $file->getVersion()),
                    'fileId'      => $file->getId(),
                    'fileVersion' => $file->getVersion(),
                    'folderId'    => $file->getFolderId(),
                    'folderPath'  => null,//$folder->getIdPath(),
                    'mediaType'   => strtolower($file->getMediaType()),
                    'createdAt'   => $file->getCreatedAt()->format('Y-m-d H:i:s'),
                    'name'       => $file->getName(),
                    'cache'       => $cacheStatus
                ];
            }
        } catch (\Exception $e) {
            $data = [];
            echo $e->getMessage();die;
        }

        return $data;
    }
}
