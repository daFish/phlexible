<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\Doctrine;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Phlexible\Bundle\TreeBundle\Entity\Route;
use Phlexible\Bundle\TreeBundle\Event\RouteEvent;
use Phlexible\Bundle\TreeBundle\Model\RouteManagerInterface;
use Phlexible\Bundle\TreeBundle\TreeEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Route manager
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class RouteManager implements RouteManagerInterface
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var EntityRepository
     */
    private $routeRepository;

    /**
     * @param EntityManager            $entityManager
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(EntityManager $entityManager, EventDispatcherInterface $eventDispatcher)
    {
        $this->entityManager = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @return EntityRepository
     */
    private function getRouteRepository()
    {
        if (null === $this->routeRepository) {
            $this->routeRepository = $this->entityManager->getRepository('PhlexibleTreeBundle:Route');
        }

        return $this->routeRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function find($id)
    {
        return $this->getRouteRepository()->find($id);
    }

    /**
     * {@inheritdoc}
     */
    public function findOneBy(array $criteria, array $orderBy = null)
    {
        return $this->getRouteRepository()->findOneBy($criteria, $orderBy);
    }

    /**
     * {@inheritdoc}
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->getRouteRepository()->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * {@inheritdoc}
     */
    public function findAll()
    {
        return $this->getRouteRepository()->findAll();
    }

    /**
     * {@inheritdoc}
     */
    public function updateRoute(Route $route, $flush = true)
    {
        $event = new RouteEvent($route);
        $isUpdate = false;
        if ($this->entityManager->contains($route)) {
            $isUpdate = true;
            $this->eventDispatcher->dispatch(TreeEvents::BEFORE_UPDATE_ROUTE, $event);
        } else {
            $this->eventDispatcher->dispatch(TreeEvents::BEFORE_CREATE_ROUTE, $event);
        }

        $this->entityManager->persist($route);

        if ($flush) {
            $this->entityManager->flush($route);
        }

        $event = new RouteEvent($route);
        if ($isUpdate) {
            $this->eventDispatcher->dispatch(TreeEvents::UPDATE_ROUTE, $event);
        } else {
            $this->eventDispatcher->dispatch(TreeEvents::CREATE_ROUTE, $event);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteRoute(Route $route, $flush = true)
    {
        $event = new RouteEvent($route);
        $this->eventDispatcher->dispatch(TreeEvents::BEFORE_DELETE_ROUTE, $event);

        $this->entityManager->remove($route);

        if ($flush) {
            $this->entityManager->flush();
        }

        $event = new RouteEvent($route);
        $this->eventDispatcher->dispatch(TreeEvents::DELETE_ROUTE, $event);

        return $this;
    }
}
