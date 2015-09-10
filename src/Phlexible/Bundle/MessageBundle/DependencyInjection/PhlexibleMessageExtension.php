<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\MessageBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Messages extension
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class PhlexibleMessageExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $configuration = $this->getConfiguration($config, $container);
        $config = $this->processConfiguration($configuration, $config);

        $handlers = array(
            new Reference('phlexible_message.handler.message_manager')
        );
        if ($config['use_log_handler']) {
            $handlers[] = new Reference('phlexible_message.handler.log');
        }
        if ($container->getParameter('kernel.debug')) {
            $handlers[] = new Reference('phlexible_message.handler.debug');
        }
        $container->findDefinition('phlexible_message.message_handler')->replaceArgument(0, $handlers);

        if ('custom' !== $config['message_db_driver']) {
            $loader->load(sprintf('message_%s.yml', $config['message_db_driver']));
            $container->setParameter($this->getAlias() . '.message_backend_type_' . $config['message_db_driver'], true);

            if ('elastica' === $config['message_db_driver']) {
                $container->setParameter('phlexible_message.elastica_index_name', $config['elastica_index_name']);
                $container->setParameter('phlexible_message.elastica_type_name', $config['elastica_type_name']);
            }
        }

        if ('custom' !== $config['filter_db_driver']) {
            $loader->load(sprintf('filter_%s.yml', $config['filter_db_driver']));
            $container->setParameter($this->getAlias() . '.filter_backend_type_' . $config['filter_db_driver'], true);
        }

        if ('custom' !== $config['subscription_db_driver']) {
            $loader->load(sprintf('subscription_%s.yml', $config['subscription_db_driver']));
            $container->setParameter($this->getAlias() . '.subscription_backend_type_' . $config['subscription_db_driver'], true);
        }

        $container->setParameter('phlexible_message.message_model_manager_name', $config['message_model_manager_name']);
        $container->setParameter('phlexible_message.filter_model_manager_name', $config['filter_model_manager_name']);
        $container->setParameter('phlexible_message.subscription_model_manager_name', $config['subscription_model_manager_name']);
        $container->setAlias('phlexible_message.message_manager', $config['service']['message_manager']);
        $container->setAlias('phlexible_message.filter_manager', $config['service']['filter_manager']);
        $container->setAlias('phlexible_message.subscription_manager', $config['service']['subscription_manager']);
    }
}
