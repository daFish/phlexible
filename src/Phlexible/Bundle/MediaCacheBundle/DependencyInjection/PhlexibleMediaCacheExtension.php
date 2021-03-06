<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\MediaCacheBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Media cache extension.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class PhlexibleMediaCacheExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
        $loader->load('queue.yml');

        $configuration = $this->getConfiguration($config, $container);
        $config = $this->processConfiguration($configuration, $config);

        $ids = array();
        foreach ($config['storages'] as $name => $storageConfig) {
            if (!isset($storageConfig['id']) || !isset($storageConfig['options'])) {
                throw new InvalidArgumentException('Storage config needs id and options.');
            }
            $storageId = $storageConfig['id'];
            $storage = $container->findDefinition($storageId);
            $storage->replaceArgument(0, $storageConfig['options']);
            $ids[$name] = new Reference($storageId);
        }

        $container->getDefinition('phlexible_media_cache.storage_manager')->replaceArgument(0, $ids);

        if ('custom' !== $config['db_driver']) {
            $loader->load(sprintf('%s.yml', $config['db_driver']));
            $container->setParameter($this->getAlias().'.backend_type_'.$config['db_driver'], true);
        }

        $container->setParameter('phlexible_media_cache.process_on_add', $config['process_on_add']);
        $container->setParameter('phlexible_media_cache.model_manager_name', $config['model_manager_name']);

        $container->setAlias('phlexible_media_cache.cache_manager', $config['service']['cache_manager']);
    }
}
