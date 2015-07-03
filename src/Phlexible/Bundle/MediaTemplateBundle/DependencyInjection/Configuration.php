<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaTemplateBundle\DependencyInjection;

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

        $rootNode
            ->children()
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
            ->end();

        return $treeBuilder;
    }
}
