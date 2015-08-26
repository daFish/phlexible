<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\TreeBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Container configuration
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
        $rootNode = $treeBuilder->root('phlexible_tree');

        $rootNode
            ->children()
                ->arrayNode('patterns')
                    ->canBeUnset()
                    ->prototype('scalar')
                    ->end()
                ->end()
                ->arrayNode('create')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('restricted')->defaultValue(false)->end()
                        ->booleanNode('use_multilanguage')->defaultValue(false)->end()
                    ->end()
                ->end()
                ->arrayNode('portlet')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->integerNode('num_items')->defaultValue(10)->end()
                    ->end()
                ->end()
                ->arrayNode('publish')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('comment_required')->defaultValue(false)->end()
                        ->booleanNode('confirm_required')->defaultValue(false)->end()
                        ->booleanNode('cross_language_publish_offline')->defaultValue(false)->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
