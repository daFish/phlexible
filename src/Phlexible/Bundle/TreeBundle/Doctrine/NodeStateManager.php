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
use Phlexible\Bundle\SiterootBundle\Model\SiterootManagerInterface;
use Phlexible\Bundle\TreeBundle\Entity\NodeState;
use Phlexible\Bundle\TreeBundle\Event\NodeStateEvent;
use Phlexible\Bundle\TreeBundle\Model\NodeStateManagerInterface;
use Phlexible\Bundle\TreeBundle\Node\NodeHasher;
use Phlexible\Bundle\TreeBundle\TreeEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Node manager
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class NodeStateManager implements NodeStateManagerInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var EntityRepository
     */
    private $nodeStateRepository;

    /**
     * @param EntityManagerInterface   $entityManager
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(EntityManagerInterface $entityManager, EventDispatcherInterface $eventDispatcher)
    {
        $this->entityManager = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @return EntityRepository
     */
    private function getNodeStateRepository()
    {
        if (null === $this->nodeStateRepository) {
            $this->nodeStateRepository = $this->entityManager->getRepository('PhlexibleTreeBundle:NodeState');
        }

        return $this->nodeStateRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function findBy(array $criteria)
    {
        return $this->getNodeStateRepository()->findBy($criteria);
    }

    /**
     * {@inheritdoc}
     */
    public function findOneBy(array $criteria)
    {
        return $this->getNodeStateRepository()->findOneBy($criteria);
    }

    /**
     * {@inheritdoc}
     */
    public function updateState(NodeState $nodeOnline)
    {
        $event = new NodeStateEvent($nodeOnline);
        if ($this->entityManager->contains($nodeOnline)) {
            $beforeEventName = TreeEvents::BEFORE_UPDATE_STATE;
            $eventName = TreeEvents::UPDATE_STATE;
        } else {
            $beforeEventName = TreeEvents::BEFORE_CREATE_STATE;
            $eventName = TreeEvents::CREATE_STATE;

        }
        if ($this->eventDispatcher->dispatch($beforeEventName, $event)->isPropagationStopped()) {
            return false;
        }

        $this->entityManager->persist($nodeOnline);
        $this->entityManager->flush($nodeOnline);

        $event = new NodeStateEvent($nodeOnline);
        $this->eventDispatcher->dispatch($eventName, $event);

        return $nodeOnline;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteState(NodeState $nodeOnline)
    {
        $event = new NodeStateEvent($nodeOnline);
        if ($this->eventDispatcher->dispatch(TreeEvents::BEFORE_DELETE_STATE, $event)->isPropagationStopped()) {
            return false;
        }

        $this->entityManager->remove($nodeOnline);
        $this->entityManager->flush($nodeOnline);

        $event = new NodeStateEvent($nodeOnline);
        $this->eventDispatcher->dispatch(TreeEvents::DELETE_STATE, $event);

        return $this;
    }
}
