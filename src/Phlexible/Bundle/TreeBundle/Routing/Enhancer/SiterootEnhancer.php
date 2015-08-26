<?php

namespace Phlexible\Bundle\TreeBundle\Routing\Enhancer;

use Phlexible\Bundle\TreeBundle\Entity\Route;
use Phlexible\Component\Site\Model\SiteManagerInterface;
use Symfony\Cmf\Component\Routing\Enhancer\RouteEnhancerInterface;
use Symfony\Component\HttpFoundation\Request;

class SiterootEnhancer implements RouteEnhancerInterface
{
    /**
     * @var SiteManagerInterface
     */
    private $siteManager;

    /**
     * @param \Phlexible\Component\Site\Model\SiteManagerInterface $siteManager
     */
    public function __construct(SiteManagerInterface $siteManager)
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
