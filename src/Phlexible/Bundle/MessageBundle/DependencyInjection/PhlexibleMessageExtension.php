<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
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

        $handlers = [
            new Reference('phlexible_message.handler.message_manager')
        ];
        if ($config['use_log_handler']) {
            $handlers[] = new Reference('phlexible_message.handler.log');
        }
        if ($container->getParameter('kernel.debug')) {
            $handlers[] = new Reference('phlexible_message.handler.debug');
        }
        $container->findDefinition('phlexible_message.handlers')->replaceArgument(0, $handlers);

        if ($config['message_manager'] === 'doctrine') {
            $loader->load('doctrine_message.yml');
            $container->setAlias('phlexible_message.message_manager', 'phlexible_message.doctrine.message_manager');
        } elseif ($config['message_manager'] === 'elastica') {
            $loader->load('elastica_message.yml');
            $container->setAlias('phlexible_message.message_manager', 'phlexible_message.elastica.message_manager');
        } else {
            throw new \InvalidArgumentException('message_manager needs to be doctrine or elastica');
        }

        $container->setParameter('phlexible_message.elastica_index_name', $config['elastica_index_name']);
        $container->setParameter('phlexible_message.elastica_type_name', $config['elastica_type_name']);

        $loader->load('doctrine_filter.yml');
        $container->setAlias('phlexible_message.filter_manager', 'phlexible_message.doctrine.filter_manager');

        $loader->load('doctrine_subscription.yml');
        $container->setAlias('phlexible_message.subscription_manager', 'phlexible_message.doctrine.subscription_manager');
    }
}
