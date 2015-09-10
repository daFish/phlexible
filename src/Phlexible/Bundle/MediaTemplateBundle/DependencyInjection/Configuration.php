<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaTemplateBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Media template configuration
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
        $rootNode = $treeBuilder->root('phlexible_media_template');

        $supportedDrivers = array('file', 'custom');

        $rootNode
            ->children()
                ->scalarNode('db_driver')
                    ->validate()
                        ->ifNotInArray($supportedDrivers)
                        ->thenInvalid('The driver %s is not supported. Please choose one of '.json_encode($supportedDrivers))
                    ->end()
                    ->defaultValue('file')
                    ->cannotBeOverwritten()
                    ->cannotBeEmpty()
                ->end()
                ->arrayNode('dumper')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('filesystem_dir')
                            ->defaultValue('%kernel.root_dir%/Resources/mediatemplates')
                            ->info('Filesystem directory for dumped media templates.')
                        ->end()
                        ->scalarNode('puli_resource_dir')
                            ->defaultValue('/app/mediatemplates')
                            ->info('Puli resource directory for dumped media templates.')
                        ->end()
                        ->scalarNode('default_type')
                            ->defaultValue('xml')
                            ->info('Default type for dumped media templates.')
                        ->end()
                    ->end()
                ->end()
            ->end()
            // Using the custom driver requires changing the manager services
            ->validate()
                ->ifTrue(function($v){return 'custom' === $v['db_driver'] && 'phlexible_media_template.template_manager.default' === $v['service']['template_manager'];})
                ->thenInvalid('You need to specify your own template manager service when using the "custom" driver.')
            ->end();

        $this->addServiceSection($rootNode);

        return $treeBuilder;
    }

    private function addServiceSection(ArrayNodeDefinition $node)
    {
        $node
            ->addDefaultsIfNotSet()
            ->children()
                ->arrayNode('service')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('template_manager')->defaultValue('phlexible_media_template.template_manager.default')->end()
                    ->end()
                ->end()
            ->end();
    }
}
