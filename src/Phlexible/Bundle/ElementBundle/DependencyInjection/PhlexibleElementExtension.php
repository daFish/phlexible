<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Element extension
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class PhlexibleElementExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');
        $loader->load('tasks.yml');
        $loader->load('content.yml');
        $loader->load('proxy.yml');
        $loader->load('doctrine.yml');

        $container->setAlias('phlexible_element.element_manager', 'phlexible_element.doctrine.element_manager');
        $container->setAlias('phlexible_element.element_version_manager', 'phlexible_element.doctrine.element_version_manager');
        $container->setAlias('phlexible_element.element_source_manager', 'phlexible_element.doctrine.element_source_manager');
    }
}
