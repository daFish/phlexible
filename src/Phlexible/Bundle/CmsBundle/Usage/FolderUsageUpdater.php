<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\CmsBundle\Usage;

use Doctrine\ORM\EntityManager;
use Phlexible\Bundle\MediaManagerBundle\Entity\FolderUsage;
use Phlexible\Bundle\TreeBundle\Node\NodeContext;
use Phlexible\Component\Volume\VolumeManager;

/**
 * File usage
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class FolderUsageUpdater
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var VolumeManager
     */
    private $volumeManager;

    /**
     * @param EntityManager $entityManager
     * @param VolumeManager $volumeManager
     */
    public function __construct(EntityManager $entityManager, VolumeManager $volumeManager)
    {
        $this->entityManager = $entityManager;
        $this->volumeManager = $volumeManager;
    }

    /**
     * @param NodeContext $node
     * @param bool        $flush
     *
     * @return array
     */
    public function updateUsage(NodeContext $node, $flush = true)
    {
        $nodeLinkRepository = $this->entityManager->getRepository('PhlexibleTreeBundle:NodeLink');
        $folderUsageRepository = $this->entityManager->getRepository('PhlexibleMediaManagerBundle:FolderUsage');

        $folderLinks = $nodeLinkRepository->findBy(array('nodeId' => $node->getId(), 'type' => 'folder'));

        $flags = array();

        foreach ($folderLinks as $folderLink) {
            $folderId = $folderLink->getTarget();

            if (!isset($flags[$folderId])) {
                $flags[$folderId] = 0;
            }

            $versions = $node->getContentVersions();
            sort($versions);
            $latestVersion = end($versions);

            $linkVersion = $folderLink->getVersion();
            $old = true;

            // add flag STATUS_LATEST if this link is a link to the latest element version
            if ($linkVersion === $latestVersion) {
                $flags[$folderId] |= FolderUsage::STATUS_LATEST;
                $old = false;
            }

            // add flag STATUS_ONLINE if this link is used in an online node version
            foreach ($node->getPublishedVersions() as $language => $onlineVersion) {
                if ($onlineVersion === $linkVersion) {
                    $flags[$folderId] |= FolderUsage::STATUS_ONLINE;
                    $old = false;
                    break;
                }
            }

            // add flag STATUS_OLD if this link is neither used in latest element version nor online version
            if ($old) {
                $flags[$folderId] |= FolderUsage::STATUS_OLD;
            }
        }

        foreach ($flags as $folderId => $flag) {
            $volume = $this->volumeManager->getByFolderId($folderId);
            $folder = $volume->findFolder($folderId);

            $folderUsage = $folderUsageRepository->findOneBy(array('folder' => $folder, 'usageType' => 'node', 'usageId' => $node->getId()));
            if (!$folderUsage) {
                $folderUsage = new FolderUsage($folder, 'node', $node->getId(), $flag);
                $this->entityManager->persist($folderUsage);
            } else {
                if ($flag) {
                    $folderUsage->setStatus($flag);
                } else {
                    $this->entityManager->remove($folderUsage);
                }
            }
        }

        if ($flush) {
            $this->entityManager->flush();
        }
    }
}
