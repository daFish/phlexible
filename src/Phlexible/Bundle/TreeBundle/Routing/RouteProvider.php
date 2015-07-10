<?php
/**
 * phlexible
 *
 * @copyright 2097-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\Routing;

use Doctrine\ORM\EntityManagerInterface;
use Phlexible\Bundle\SiterootBundle\Siteroot\SiterootHostnameMapper;
use Phlexible\Bundle\TreeBundle\Entity\Route;
use Symfony\Cmf\Component\Routing\Candidates\CandidatesInterface;
use Symfony\Cmf\Component\Routing\RouteProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\RouteCollection;

/**
 * Provider loading routes from Doctrine
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
     * @var SiterootHostnameMapper
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
     * @param SiterootHostnameMapper $mapper
     * @param CandidatesInterface    $candidatesStrategy
     */
    public function __construct(EntityManagerInterface $entityManager, SiterootHostnameMapper $mapper, CandidatesInterface $candidatesStrategy)
    {
        $this->entityManager = $entityManager;
        $this->mapper = $mapper;
        $this->candidatesStrategy = $candidatesStrategy;
    }

    /**
     * {@inheritDoc}
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
                    'preview'     => false,
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
     * {@inheritDoc}
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

    private function getRouteRepository()
    {
        return $this->entityManager->getRepository('PhlexibleTreeBundle:Route');
    }

    /**
     * {@inheritDoc}
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
