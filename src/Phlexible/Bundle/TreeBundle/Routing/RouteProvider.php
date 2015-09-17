<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\TreeBundle\Routing;

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Phlexible\Bundle\TreeBundle\Entity\Route;
use Phlexible\Component\Site\Site\SiteHostnameMapper;
use Symfony\Cmf\Component\Routing\Candidates\CandidatesInterface;
use Symfony\Cmf\Component\Routing\RouteProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\RouteCollection;

/**
 * Provider loading routes from Doctrine.
 *
 * This is <strong>NOT</strong> not a doctrine repository but just the route
 * provider for the NestedMatcher. (you could of course implement this
 * interface in a repository class, if you need that)
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class RouteProvider implements RouteProviderInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var SiteHostnameMapper
     */
    private $mapper;

    /**
     * @var CandidatesInterface
     */
    private $candidatesStrategy;

    /**
     * @var int
     */
    private $routeCollectionLimit = 0;

    /**
     * Constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param SiteHostnameMapper     $mapper
     * @param CandidatesInterface    $candidatesStrategy
     */
    public function __construct(EntityManagerInterface $entityManager, SiteHostnameMapper $mapper, CandidatesInterface $candidatesStrategy)
    {
        $this->entityManager = $entityManager;
        $this->mapper = $mapper;
        $this->candidatesStrategy = $candidatesStrategy;
    }

    /**
     * {@inheritdoc}
     */
    public function getRouteCollectionForRequest(Request $request)
    {
        $collection = new RouteCollection();

        /*
        $candidates = $this->candidatesStrategy->getCandidates($request);
        if (empty($candidates)) {
            return $collection;
        }
        */

        //foreach ($candidates as $candidate) {
            foreach ($this->getRouteRepository()->findBy(array('path' => $request->getPathInfo())) as $route) {
                /* @var $route Route */
                $route->addDefaults(array(
                    '_controller' => 'PhlexibleCmsBundle:Online:index',
                    'preview' => false,
                ));
                $this->entityManager->detach($route);
                $route->setHost($this->mapper->toLocal($route->getHost()));
                $collection->add($route->getName(), $route);

                //$collection->add('preview_'.$treeNode->getId(), $treeNode);
            }
        //}

        return $collection;
    }

    /**
     * {@inheritdoc}
     */
    public function getRouteByName($name)
    {
        //if (!$this->candidatesStrategy->isCandidate($name)) {
        //    throw new RouteNotFoundException(sprintf('Route "%s" is not handled by this route provider', $name));
        //}

        $route = $this->getRouteRepository()->findOneBy(array('name' => $name));
        if (!$route) {
            throw new RouteNotFoundException("No route found for name '$name'");
        }

        /* @var $route Route */
        $this->entityManager->detach($route);
        $route->setHost($this->mapper->toLocal($route->getHost()));

        return $route;
    }

    /**
     * @return ObjectRepository
     */
    private function getRouteRepository()
    {
        return $this->entityManager->getRepository('PhlexibleTreeBundle:Route');
    }

    /**
     * {@inheritdoc}
     */
    public function getRoutesByNames($names = null)
    {
        if (null === $names) {
            if (0 === $this->routeCollectionLimit) {
                return array();
            }

            return $this->getRouteRepository()->findBy(array(), null, $this->routeCollectionLimit ?: null);
        }

        $routes = array();
        foreach ($names as $name) {
            // TODO: if we do findByName with multivalue, we need to filter with isCandidate afterwards
            try {
                $routes[] = $this->getRouteByName($name);
            } catch (RouteNotFoundException $e) {
                // not found
            }
        }

        return $routes;
    }
}
