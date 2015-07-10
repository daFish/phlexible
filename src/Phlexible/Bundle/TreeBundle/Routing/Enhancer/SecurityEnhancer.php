<?php

namespace Phlexible\Bundle\TreeBundle\Routing\Enhancer;

use Phlexible\Bundle\TreeBundle\Node\NodeContext;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Cmf\Component\Routing\Enhancer\RouteEnhancerInterface;
use Symfony\Component\HttpFoundation\Request;

class SecurityEnhancer implements RouteEnhancerInterface
{
    /**
     * {@inheritdoc}
     */
    public function enhance(array $defaults, Request $request)
    {
        if (!isset($defaults['node']) || !$defaults['node'] instanceof NodeContext) {
            return $defaults;
        }

        $node = $defaults['node'];

        if ('true' === $expression = $node->getNode()->getSecurityExpression()) {
            return $defaults;
        }

        $configuration = new Security(array('expression' => $expression));
        $defaults['_security'] = $configuration;

        return $defaults;
    }
}
