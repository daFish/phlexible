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
 * Add infobars pass.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class AddInfobarsPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $infobars = array();
        foreach ($container->findTaggedServiceIds('phlexible_dashboard.infobar') as $id => $attributes) {
            $infobars[] = new Reference($id);
        }

        $container->getDefinition('phlexible_dashboard.infobars')->replaceArgument(0, $infobars);
    }
}
