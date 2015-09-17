<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\TreeBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Add field mappers pass.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class AddFieldMappersPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $mappers = array();
        foreach ($container->findTaggedServiceIds('phlexible_tree.field_mapper') as $id => $attributes) {
            $mappers[] = new Reference($id);
        }
        $mapper = $container->findDefinition('phlexible_tree.field_mapper');
        $mapper->replaceArgument(2, $mappers);
    }
}
