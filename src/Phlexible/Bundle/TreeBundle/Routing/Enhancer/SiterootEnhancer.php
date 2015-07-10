<?php

namespace Phlexible\Bundle\TreeBundle\Routing\Enhancer;

use Phlexible\Bundle\SiterootBundle\Model\SiterootManagerInterface;
use Phlexible\Bundle\TreeBundle\Entity\Route;
use Symfony\Cmf\Component\Routing\Enhancer\RouteEnhancerInterface;
use Symfony\Component\HttpFoundation\Request;

class SiterootEnhancer implements RouteEnhancerInterface
{
    /**
     * @var SiterootManagerInterface
     */
    private $siteManager;

    /**
     * @param SiterootManagerInterface $siteManager
     */
    public function __construct(SiterootManagerInterface $siteManager)
    {
        $this->siteManager = $siteManager;
    }

    /**
     * {@inheritdoc}
     */
    public function enhance(array $defaults, Request $request)
    {
        if (!isset($defaults['_route_object']) || !$defaults['_route_object'] instanceof Route) {
            return $defaults;
        }

        $route = $defaults['_route_object'];

        $siterootId = $route->getDefault('siterootId');
        $site = $this->siteManager->find($siterootId);

        $defaults['siteroot'] = $site;

        return $defaults;
    }
}
