<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\Doctrine;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Phlexible\Bundle\ElementBundle\Model\ElementHistoryManagerInterface;
use Phlexible\Bundle\TreeBundle\Entity\TreeNodeOnline;
use Phlexible\Bundle\TreeBundle\Model\StateManagerInterface;
use Phlexible\Bundle\TreeBundle\Model\TreeNodeInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * State manager
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class StateManager implements StateManagerInterface
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var ElementHistoryManagerInterface
     */
    private $historyManager;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var EntityRepository
     */
    private $treeNodeOnlineRepository;

    /**
     * @param EntityManager                  $entityManager
     * @param ElementHistoryManagerInterface $historyManager
     * @param EventDispatcherInterface       $dispatcher
     */
    public function __construct(
        EntityManager $entityManager,
        ElementHistoryManagerInterface $historyManager,
        EventDispatcherInterface $dispatcher)
    {
        $this->entityManager = $entityManager;
        $this->historyManager = $historyManager;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @return EntityRepository
     */
    private function getTeaserOnlineRepository()
    {
        if (null === $this->treeNodeOnlineRepository) {
            $this->treeNodeOnlineRepository = $this->entityManager->getRepository('PhlexibleTreeBundle:TreeNodeOnline');
        }

        return $this->treeNodeOnlineRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function findByTreeNode(TreeNodeInterface $treeNode)
    {
        return $this->getTeaserOnlineRepository()->findBy(array('treeNode' => $treeNode->getId()));
    }

    /**
     * {@inheritdoc}
     */
    public function findOneByTreeNodeAndLanguage(TreeNodeInterface $treeNode, $language)
    {
        return $this->getTeaserOnlineRepository()->findOneBy(array('treeNode' => $treeNode->getId(), 'language' => $language));
    }

    /**
     * {@inheritdoc}
     */
    public function isPublished(TreeNodeInterface $treeNode, $language)
    {
        return $this->findOneByTreeNodeAndLanguage($treeNode, $language) ? true : false;
    }

    /**
     * {@inheritdoc}
     */
    public function getPublishedLanguages(TreeNodeInterface $treeNode)
    {
        $language = array();
        foreach ($this->findByTreeNode($treeNode) as $treeNodeOnline) {
            $language[] = $treeNodeOnline->getLanguage();
        }

        return $language;
    }

    /**
     * {@inheritdoc}
     */
    public function getPublishedVersions(TreeNodeInterface $treeNode)
    {
        $versions = array();
        foreach ($this->findByTreeNode($treeNode) as $treeNodeOnline) {
            $versions[$treeNodeOnline->getLanguage()] = $treeNodeOnline->getVersion();
        }

        return $versions;
    }

    /**
     * {@inheritdoc}
     */
    public function getPublishedVersion(TreeNodeInterface $treeNode, $language)
    {
        $treeNodeOnline = $this->findOneByTreeNodeAndLanguage($treeNode, $language);
        if (!$treeNodeOnline) {
            return null;
        }

        return $treeNodeOnline->getLanguage();
    }

    /**
     * {@inheritdoc}
     */
    public function isAsync(TreeNodeInterface $node, $language)
    {
        // TODO: implement

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function publish(TreeNodeInterface $treeNode, $version, $language, $userId, $comment = null)
    {
        $treeNodeOnline = $this->getTeaserOnlineRepository()->findOneBy(array('treeNode' => $treeNode, 'language' => $language));
        if (!$treeNodeOnline) {
            $treeNodeOnline = new TreeNodeOnline();
            $treeNodeOnline
                ->setTreeNode($treeNode);
        }

        $treeNodeOnline
            ->setLanguage($language)
            ->setVersion($version)
            ->setPublishedAt(new \DateTime())
            ->setPublishUserId($userId);

        $this->entityManager->persist($treeNodeOnline);
        $this->entityManager->flush($treeNodeOnline);

        return $treeNodeOnline;
    }

    /**
     * {@inheritdoc}
     */
    public function setOffline(TreeNodeInterface $treeNode, $language)
    {
        $treeNodeOnline = $this->getTeaserOnlineRepository()->findOneBy(array('treeNode' => $treeNode, 'language' => $language));

        if ($treeNodeOnline) {
            $this->entityManager->remove($treeNodeOnline);
            $this->entityManager->flush();
        }
    }
}
