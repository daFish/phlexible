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

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Messages configuration
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
        $rootNode = $treeBuilder->root('phlexible_message');

        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
                ->booleanNode('use_log_handler')->defaultValue(false)->end()
                ->scalarNode('message_manager')->defaultValue('doctrine')->end()
                ->scalarNode('elastica_index_name')->defaultNull()->end()
                ->scalarNode('elastica_type_name')->defaultValue('message')->end()
            ->end()
            ->validate()
                ->ifTrue(function($v){return 'elastica' === $v['message_manager'] && null === $v['elastica_index_name'];})
                ->thenInvalid('You need to specify the elastica_index_name for the "elastica" driver.')
            ->end()
            ->validate()
                ->ifTrue(function($v){return 'elastica' === $v['message_manager'] && null === $v['elastica_type_name'];})
                ->thenInvalid('You need to specify the elastica_type_name for the "elastica" driver.')
            ->end();

        return $treeBuilder;
    }
}
