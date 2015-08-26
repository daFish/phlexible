<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\CmsBundle\Usage;

use Doctrine\ORM\EntityManager;
use Phlexible\Bundle\MediaManagerBundle\Entity\FileUsage;
use Phlexible\Bundle\TreeBundle\Node\NodeContext;
use Phlexible\Component\Volume\Model\VolumeManagerInterface;

/**
 * File usage updater
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class FileUsageUpdater
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var VolumeManagerInterface
     */
    private $volumeManager;

    /**
     * @param EntityManager          $entityManager
     * @param VolumeManagerInterface $volumeManager
     */
    public function __construct(EntityManager $entityManager, VolumeManagerInterface $volumeManager)
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
        $fileUsageRepository = $this->entityManager->getRepository('PhlexibleMediaManagerBundle:FileUsage');

        $fileLinks = $nodeLinkRepository->findBy(array('nodeId' => $node->getId(), 'type' => 'file'));

        $flags = array();

        foreach ($fileLinks as $fileLink) {
            $fileParts = explode(';', $fileLink->getTarget());
            $fileId = $fileParts[0];
            $fileVersion = 1;
            if (isset($fileParts[1])) {
                $fileVersion = $fileParts[1];
            }

            if (!isset($flags[$fileId][$fileVersion])) {
                $flags[$fileId][$fileVersion] = 0;
            }

            $versions = $node->getContentVersions();
            sort($versions);
            $latestVersion = end($versions);

            $linkVersion = $fileLink->getVersion();
            $old = true;

            // add flag STATUS_LATEST if this link is a link to the latest element version
            if ($linkVersion === $latestVersion) {
                $flags[$fileId][$fileVersion] |= FileUsage::STATUS_LATEST;
                $old = false;
            }

            // add flag STATUS_ONLINE if this link is used in an online node version
            foreach ($node->getPublishedVersions() as $language => $onlineVersion) {
                if ($onlineVersion === $linkVersion) {
                    $flags[$fileId][$fileVersion] |= FileUsage::STATUS_ONLINE;
                    $old = false;
                    break;
                }
            }

            // add flag STATUS_OLD if this link is neither used in latest element version nor online version
            if ($old) {
                $flags[$fileId][$fileVersion] |= FileUsage::STATUS_OLD;
            }
        }

        foreach ($flags as $fileId => $fileVersions) {
            foreach ($fileVersions as $fileVersion => $flag) {
                try {
                    $volume = $this->volumeManager->getByFileId($fileId);
                    $file = $volume->findFile($fileId, $fileVersion);
                } catch (\Exception $e) {
                    continue;
                }

                $qb = $fileUsageRepository->createQueryBuilder('fu');
                $qb
                    ->select('fu')
                    ->join('fu.file', 'f')
                    ->where($qb->expr()->eq('fu.usageType', $qb->expr()->literal('node')))
                    ->andWhere($qb->expr()->eq('fu.usageId', $node->getId()))
                    ->andWhere($qb->expr()->eq('f.id', $qb->expr()->literal($file->getId())))
                    ->andWhere($qb->expr()->eq('f.version', $file->getVersion()))
                    ->setMaxResults(1);
                $fileUsages = $qb->getQuery()->getResult();
                if (!count($fileUsages)) {
                    if (!$flag) {
                        continue;
                    }
                    $folderUsage = new FileUsage($file, 'node', $node->getId(), $flag);
                    $this->entityManager->persist($folderUsage);
                } else {
                    $fileUsage = current($fileUsages);

                    if ($flag) {
                        $fileUsage->setStatus($flag);
                    } else {
                        $this->entityManager->remove($fileUsage);
                    }
                }
            }
        }

        if ($flush) {
            $this->entityManager->flush();
        }
    }
}
