<?php

namespace Phlexible\Bundle\TreeBundle\Routing\Enhancer;

use Phlexible\Bundle\TreeBundle\Entity\Route;
use Phlexible\Bundle\TreeBundle\Model\TreeManagerInterface;
use Symfony\Cmf\Component\Routing\Enhancer\RouteEnhancerInterface;
use Symfony\Component\HttpFoundation\Request;

class NodeEnhancer implements RouteEnhancerInterface
{
    /**
     * @var TreeManagerInterface
     */
    private $treeManager;

    /**
     * @param TreeManagerInterface $treeManager
     */
    public function __construct(TreeManagerInterface $treeManager)
    {
        $this->treeManager = $treeManager;
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

        $nodeId = $route->getDefault('nodeId');
        $node = $this->treeManager->getByNodeId($nodeId)->get($nodeId);
        $node->getTree()->setDefaultLanguage($request->getLocale());

        $defaults['node'] = $node;

        return $defaults;
    }
}
