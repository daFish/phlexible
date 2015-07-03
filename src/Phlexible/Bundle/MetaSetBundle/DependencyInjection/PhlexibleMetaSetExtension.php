<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MetaSetBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Meta set extension
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class PhlexibleMetaSetExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');
        $loader->load('file.yml');

        $configuration = $this->getConfiguration($config, $container);
        $config = $this->processConfiguration($configuration, $config);

        $container->setParameter('phlexible_meta_set.languages.default', $config['languages']['default']);
        $container->setParameter('phlexible_meta_set.languages.available', $config['languages']['available']);
        $container->setParameter('phlexible_meta_set.suggest.seperator', $config['suggest']['seperator']);
        $container->setParameter('phlexible_meta_set.dumper.filesystem_dir', $config['dumper']['filesystem_dir']);
        $container->setParameter('phlexible_meta_set.dumper.puli_resource_dir', $config['dumper']['puli_resource_dir']);
        $container->setParameter('phlexible_meta_set.dumper.default_type', $config['dumper']['default_type']);

        $container->setAlias('phlexible_meta_set.meta_set_manager', 'phlexible_meta_set.file.meta_set_manager');
    }
}
