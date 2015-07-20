<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\Doctrine;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Phlexible\Bundle\ElementBundle\Model\ElementHistoryManagerInterface;
use Phlexible\Bundle\SiterootBundle\Model\SiterootManagerInterface;
use Phlexible\Bundle\TreeBundle\Entity\NodeState;
use Phlexible\Bundle\TreeBundle\Event\NodeEvent;
use Phlexible\Bundle\TreeBundle\Event\NodeOnlineEvent;
use Phlexible\Bundle\TreeBundle\Model\NodeManagerInterface;
use Phlexible\Bundle\TreeBundle\Model\NodeInterface;
use Phlexible\Bundle\TreeBundle\Node\NodeHasher;
use Phlexible\Bundle\TreeBundle\TreeEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Node manager
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class NodeManager implements NodeManagerInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var NodeHasher
     */
    private $nodeHasher;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var EntityRepository
     */
    private $nodeRepository;

    /**
     * @var EntityRepository
     */
    private $nodeOnlineRepository;

    /**
     * @param EntityManagerInterface   $entityManager
     * @param SiterootManagerInterface $siterootManager
     * @param NodeHasher               $nodeHasher
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        SiterootManagerInterface $siterootManager,
        NodeHasher $nodeHasher,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->entityManager = $entityManager;
        $this->siterootManager = $siterootManager;
        $this->nodeHasher = $nodeHasher;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @return EntityRepository
     */
    private function getNodeRepository()
    {
        if (null === $this->nodeRepository) {
            $this->nodeRepository = $this->entityManager->getRepository('PhlexibleTreeBundle:Node');
        }

        return $this->nodeRepository;
    }

    /**
     * @return EntityRepository
     */
    private function getNodeOnlineRepository()
    {
        if (null === $this->nodeOnlineRepository) {
            $this->nodeOnlineRepository = $this->entityManager->getRepository('PhlexibleTreeBundle:NodeState');
        }

        return $this->nodeOnlineRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function find($id)
    {
        return $this->getNodeRepository()->find($id);
    }

    /**
     * {@inheritdoc}
     */
    public function findOneBy(array $criteria, array $orderBy = null)
    {
        return $this->getNodeRepository()->findOneBy($criteria, $orderBy);
    }

    /**
     * {@inheritdoc}
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->getNodeRepository()->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * {@inheritdoc}
     */
    public function findStateBy(array $criteria)
    {
        return $this->getNodeOnlineRepository()->findBy($criteria);
    }

    /**
     * {@inheritdoc}
     */
    public function findOneStateBy(array $criteria)
    {
        return $this->getNodeOnlineRepository()->findOneBy($criteria);
    }

    /**
     * {@inheritdoc}
     */
    public function hashNode(NodeInterface $node, $version, $language)
    {
        return $this->nodeHasher->hashNode($node, $version, $language);
    }

    /**
     * {@inheritdoc}
     */
    public function updateNode(NodeInterface $node, $flush = true)
    {
        $event = new NodeEvent($node);
        if ($this->entityManager->contains($node)) {
            $isUpdate = true;
            $eventName = TreeEvents::BEFORE_UPDATE_NODE;
        } else {
            $isUpdate = false;
            $eventName = TreeEvents::BEFORE_CREATE_NODE;

        }
        if ($this->eventDispatcher->dispatch($eventName, $event)->isPropagationStopped()) {
            return false;
        }

        $this->entityManager->persist($node);
        if ($flush) {
            $this->entityManager->flush($node);
        }

        $event = new NodeEvent($node);
        if ($isUpdate) {
            $eventName = TreeEvents::BEFORE_UPDATE_NODE;
            $historyName = ElementHistoryManagerInterface::ACTION_UPDATE_NODE;
        } else {
            $eventName = TreeEvents::BEFORE_CREATE_NODE;
            $historyName = ElementHistoryManagerInterface::ACTION_CREATE_NODE;
        }

        $this->eventDispatcher->dispatch($eventName, $event);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function updateState(NodeState $nodeOnline)
    {
        $event = new NodeOnlineEvent($nodeOnline);
        if ($this->entityManager->contains($nodeOnline)) {
            $isUpdate = true;
            $eventName = TreeEvents::BEFORE_UPDATE_STATE;
        } else {
            $isUpdate = false;
            $eventName = TreeEvents::BEFORE_CREATE_STATE;

        }
        if ($this->eventDispatcher->dispatch($eventName, $event)->isPropagationStopped()) {
            return false;
        }

        $this->entityManager->persist($nodeOnline);
        $this->entityManager->flush($nodeOnline);

        $event = new NodeOnlineEvent($nodeOnline);
        if ($isUpdate) {
            $eventName = TreeEvents::UPDATE_STATE;
            $historyName = ElementHistoryManagerInterface::ACTION_UPDATE_NODE;
        } else {
            $eventName = TreeEvents::CREATE_STATE;
            $historyName = ElementHistoryManagerInterface::ACTION_CREATE_NODE;
        }

        $this->eventDispatcher->dispatch($eventName, $event);

        return $nodeOnline;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteNode(NodeInterface $node)
    {
        $event = new NodeEvent($node);
        if ($this->eventDispatcher->dispatch(TreeEvents::BEFORE_DELETE_NODE, $event)->isPropagationStopped()) {
            return false;
        }

        $this->entityManager->persist($node);
        $this->entityManager->flush($node);

        $event = new NodeEvent($node);
        $this->eventDispatcher->dispatch(TreeEvents::BEFORE_DELETE_NODE, $event);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteState(NodeState $nodeOnline)
    {
        $event = new NodeOnlineEvent($nodeOnline);
        if ($this->eventDispatcher->dispatch(TreeEvents::BEFORE_DELETE_STATE, $event)->isPropagationStopped()) {
            return false;
        }

        $this->entityManager->remove($nodeOnline);
        $this->entityManager->flush($nodeOnline);

        $event = new NodeOnlineEvent($nodeOnline);
        $this->eventDispatcher->dispatch(TreeEvents::BEFORE_DELETE_STATE, $event);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isInstance(NodeInterface $node)
    {
        return count($this->getInstanceNodes($node)) > 1;
    }

    /**
     * {@inheritdoc}
     */
    public function isInstanceMaster(NodeInterface $node)
    {
        return $node->getAttribute('instanceMaster', false);
    }

    /**
     * {@inheritdoc}
     */
    public function getInstanceNodes(NodeInterface $node)
    {
        return $this->findBy(array('type' => $node->getContentType(), 'typeId' => $node->getContentId()));
    }
}
