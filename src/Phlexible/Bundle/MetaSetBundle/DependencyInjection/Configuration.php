<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MetaSetBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Meta sets configuration
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('phlexible_meta_set');

        $rootNode
            ->children()
                ->arrayNode('languages')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('default')
                            ->defaultValue('en')
                            ->info('Default language for metasets.')
                        ->end()
                        ->scalarNode('available')
                            ->defaultValue('en,de')
                            ->info('Available languages for metasets.')
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('suggest')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('seperator')
                            ->defaultValue(',')
                            ->info('Separator for suggest entries.')
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('dumper')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('filesystem_dir')
                            ->defaultValue('%kernel.root_dir%/Resources/meta sets')
                            ->info('Filesystem directory for dumped metasets.')
                        ->end()
                        ->scalarNode('puli_resource_dir')
                            ->defaultValue('/app/metasets')
                            ->info('Puli resource directory for dumped meta sets.')
                        ->end()
                        ->scalarNode('default_type')
                            ->defaultValue('xml')
                            ->info('Default type for dumped meta sets.')
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
