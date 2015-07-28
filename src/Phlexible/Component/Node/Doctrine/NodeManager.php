<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\Node\Doctrine;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Phlexible\Bundle\SiterootBundle\Model\SiterootManagerInterface;
use Phlexible\Bundle\TreeBundle\TreeEvents;
use Phlexible\Component\Node\Event\NodeEvent;
use Phlexible\Component\Node\Model\NodeInterface;
use Phlexible\Component\Node\Model\NodeManagerInterface;
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
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        SiterootManagerInterface $siterootManager,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->entityManager = $entityManager;
        $this->siterootManager = $siterootManager;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @return EntityRepository
     */
    private function getNodeRepository()
    {
        if (null === $this->nodeRepository) {
            $this->nodeRepository = $this->entityManager->getRepository('Phlexible\Component\Node\Domain\Node');
        }

        return $this->nodeRepository;
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
    public function findOneByNodeType(array $instanceTypes = array(), array $criteria, array $orderBy = null)
    {
        if (count($instanceTypes) === 1) {
            $nodeRepository = $this->entityManager->getRepository($instanceTypes[0]);
        } else {
            $nodeRepository = $this->getNodeRepository();
        }

        $qb = $nodeRepository->createQueryBuilder('n')
            ->setMaxResults(1);

        foreach ($criteria as $field => $value) {
            if ($value === null) {
                $qb->andWhere($qb->expr()->isNull("n.$field"));
            } else {
                $qb->andWhere($qb->expr()->eq("n.$field", $qb->expr()->literal($value)));
            }
        }

        if ($orderBy) {
            foreach ($orderBy as $field => $dir) {
                $qb->addOrderBy("n.$field", $dir);
            }
        }

        if (count($instanceTypes) > 1) {
            $qb->andWhere("n INSTANCE OF (".implode(',', $instanceTypes).")");
        }

        $result = $qb->getQuery()->getOneOrNullResult();

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function findByNodeType(array $instanceTypes = array(), array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        if (count($instanceTypes) === 1) {
            $nodeRepository = $this->entityManager->getRepository($instanceTypes[0]);
        } else {
            $nodeRepository = $this->getNodeRepository();
        }

        $qb = $nodeRepository->createQueryBuilder('n')
            ->setMaxResults($limit)
            ->setFirstResult($offset);

        foreach ($criteria as $field => $value) {
            if ($value === null) {
                $qb->andWhere($qb->expr()->isNull("n.$field"));
            } else {
                $qb->andWhere($qb->expr()->eq("n.$field", $qb->expr()->literal($value)));
            }
        }

        if ($orderBy) {
            foreach ($orderBy as $field => $dir) {
                $qb->addOrderBy("n.$field", $dir);
            }
        }

        if (count($instanceTypes) > 1) {
            $qb->andWhere("n INSTANCE OF (".implode(',', $instanceTypes).")");
        }

        $result = $qb->getQuery()->getResult();

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function updateNode(NodeInterface $node, $flush = true)
    {
        $event = new NodeEvent($node);
        if ($this->entityManager->contains($node)) {
            $beforeEventName = TreeEvents::BEFORE_UPDATE_NODE;
            $eventName = TreeEvents::UPDATE_NODE;
        } else {
            $beforeEventName = TreeEvents::BEFORE_CREATE_NODE;
            $eventName = TreeEvents::CREATE_NODE;

        }
        if ($this->eventDispatcher->dispatch($beforeEventName, $event)->isPropagationStopped()) {
            return false;
        }

        $this->entityManager->persist($node);
        if ($flush) {
            $this->entityManager->flush($node);
        }

        $event = new NodeEvent($node);
        $this->eventDispatcher->dispatch($eventName, $event);

        return $this;
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
        $this->eventDispatcher->dispatch(TreeEvents::DELETE_NODE, $event);

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
        return $this->findBy(array('contentType' => $node->getContentType(), 'contentId' => $node->getContentId()));
    }
}
