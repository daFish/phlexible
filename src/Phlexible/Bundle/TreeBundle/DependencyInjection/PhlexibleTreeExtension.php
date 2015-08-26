<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Tree extension
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class PhlexibleTreeExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
        $loader->load('doctrine.yml');
        $loader->load('file.yml');
        $loader->load('mediators.yml');
        $loader->load('routing.yml');
        $loader->load('field_mappers.yml');
        $loader->load('link_extractors.yml');
        $loader->load('node_types.yml');

        $configuration = $this->getConfiguration($config, $container);
        $config = $this->processConfiguration($configuration, $config);

        $container->setParameter('phlexible_tree.patterns', $config['patterns']);
        $container->setParameter('phlexible_tree.create.restricted', $config['create']['restricted']);
        $container->setParameter('phlexible_tree.create.use_multilanguage', $config['create']['use_multilanguage']);
        $container->setParameter('phlexible_tree.portlet.num_items', $config['portlet']['num_items']);
        $container->setParameter('phlexible_tree.publish.comment_required', $config['publish']['comment_required']);
        $container->setParameter('phlexible_tree.publish.confirm_required', $config['publish']['confirm_required']);
        $container->setParameter(
            'phlexible_tree.publish.cross_language_publish_offline',
            $config['publish']['cross_language_publish_offline']
        );

        $container->setAlias('phlexible_tree.node_manager', 'phlexible_tree.doctrine.node_manager');
        $container->setAlias('phlexible_tree.route_manager', 'phlexible_tree.doctrine.route_manager');
        $container->setAlias('phlexible_tree.node_lock_manager', 'phlexible_tree.doctrine.node_lock_manager');
        $container->setAlias('phlexible_tree.node_change_manager', 'phlexible_tree.doctrine.node_change_manager');

        $container->setParameter('phlexible_tree.backend_type_orm', true);
    }
}
