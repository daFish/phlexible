<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\DashboardBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Add portlets pass.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class AddPortletsPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $portlets = array();
        foreach ($container->findTaggedServiceIds('phlexible_dashboard.portlet') as $id => $attributes) {
            if (!isset($attributes[0]['alias'])) {
                throw new \Exception("Missing alias on phlexible_dashboard.portlet tag $id");
            }
            $portlets[$attributes[0]['alias']] = new Reference($id);
        }
        $container->getDefinition('phlexible_dashboard.portlets')->replaceArgument(0, $portlets);
    }
}
