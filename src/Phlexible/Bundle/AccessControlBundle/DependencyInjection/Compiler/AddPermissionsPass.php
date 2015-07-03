<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\AccessControlBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Add permissions pass
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class AddPermissionsPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $permissions = $container->findDefinition('phlexible_access_control.permissions');
        foreach ($container->findTaggedServiceIds('phlexible_access_control.permission') as $id => $attributes) {
            $definition = $container->findDefinition($id);
            $permissions->addMethodCall('addProvider', array(new Reference($id)));
        }
    }
}
