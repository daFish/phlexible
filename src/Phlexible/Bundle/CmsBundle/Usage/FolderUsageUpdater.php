<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\CmsBundle\Usage;

use Doctrine\ORM\EntityManager;
use Phlexible\Bundle\ElementBundle\Entity\Element;
use Phlexible\Bundle\ElementBundle\Entity\ElementLink;
use Phlexible\Bundle\MediaManagerBundle\Entity\FolderUsage;
use Phlexible\Bundle\TeaserBundle\Doctrine\TeaserManager;
use Phlexible\Bundle\TreeBundle\Model\NodeManagerInterface;
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
     * @var NodeManagerInterface
     */
    private $nodeManager;

    /**
     * @var TeaserManager
     */
    private $teaserManager;

    /**
     * @var VolumeManager
     */
    private $volumeManager;

    /**
     * @param EntityManager        $entityManager
     * @param NodeManagerInterface $nodeManager
     * @param TeaserManager        $teaserManager
     * @param VolumeManager        $volumeManager
     */
    public function __construct(
        EntityManager $entityManager,
        NodeManagerInterface $nodeManager,
        TeaserManager $teaserManager,
        VolumeManager $volumeManager)
    {
        $this->entityManager = $entityManager;
        $this->nodeManager = $nodeManager;
        $this->teaserManager = $teaserManager;
        $this->volumeManager = $volumeManager;
    }

    /**
     * @param Element $element
     * @param bool    $flush
     *
     * @return array
     */
    public function updateUsage(Element $element, $flush = true)
    {
        $eid = $element->getEid();

        $elementLinkRepository = $this->entityManager->getRepository('PhlexibleElementBundle:ElementLink');
        $folderUsageRepository = $this->entityManager->getRepository('PhlexibleMediaManagerBundle:FolderUsage');

        $qb = $elementLinkRepository->createQueryBuilder('l');
        $qb
            ->select('l')
            ->join('l.elementVersion', 'ev')
            ->join('ev.element', 'e')
            ->where($qb->expr()->eq('e.eid', $eid))
            ->andWhere($qb->expr()->eq('l.type', $qb->expr()->literal('folder')));
        $folderLinks = $qb->getQuery()->getResult();
        /* @var $folderLinks ElementLink[] */

        $flags = array();

        foreach ($folderLinks as $folderLink) {
            $folderId = $folderLink->getTarget();

            if (!isset($flags[$folderId])) {
                $flags[$folderId] = 0;
            }

            $linkVersion = $folderLink->getElementVersion()->getVersion();
            $old = true;

            // add flag STATUS_LATEST if this link is a link to the latest element version
            if ($linkVersion === $element->getLatestVersion()) {
                $flags[$folderId] |= FolderUsage::STATUS_LATEST;
                $old = false;
            }

            // add flag STATUS_ONLINE if this link is used in an online teaser version
            $teasers = $this->teaserManager->findBy(array('typeId' => $eid, 'type' => 'element'));
            foreach ($teasers as $teaser) {
                if ($this->teaserManager->getPublishedVersion($teaser, $folderLink->getLanguage()) === $linkVersion) {
                    $flags[$folderId] |= FolderUsage::STATUS_ONLINE;
                    $old = false;
                    break;
                }
            }

            // add flag STATUS_ONLINE if this link is used in an online node version
            $nodes = $this->nodeManager->findBy(array('type' => 'element', 'typeId' => $eid));
            foreach ($nodes as $node) {
                foreach ($this->nodeManager->findOneStateBy(array('node' => $node, 'language' => $fileLink->getLanguage())) as $nodeOnline) {
                    if ($nodeOnline->getVersion() === $linkVersion) {
                        $flags[$folderId] |= FolderUsage::STATUS_ONLINE;
                        $old = false;
                        break;
                    }
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

            $folderUsage = $folderUsageRepository->findOneBy(array('folder' => $folder, 'usageType' => 'element', 'usageId' => $eid));
            if (!$folderUsage) {
                $folderUsage = new FolderUsage($folder, 'element', $eid, $flag);
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
