<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\MediaCacheBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Add workers pass
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class AddWorkersPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $ids = [];
        foreach ($container->findTaggedServiceIds('phlexible_media_cache.worker') as $id => $definition) {
            $ids[] = new Reference($id);
        }
        $container->findDefinition('phlexible_media_cache.worker.resolver')->replaceArgument(0, $ids);
    }
}
