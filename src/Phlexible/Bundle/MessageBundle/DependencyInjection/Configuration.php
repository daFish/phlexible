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

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Messages configuration.
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

        $supportedMessageDrivers = array('orm', 'elastica', 'custom');
        $supportedDrivers = array('orm', 'custom');

        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('message_db_driver')
                    ->validate()
                        ->ifNotInArray($supportedMessageDrivers)
                        ->thenInvalid('The driver %s is not supported. Please choose one of '.json_encode($supportedMessageDrivers))
                    ->end()
                    ->defaultValue('orm')
                    ->cannotBeOverwritten()
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('filter_db_driver')
                    ->validate()
                        ->ifNotInArray($supportedMessageDrivers)
                        ->thenInvalid('The driver %s is not supported. Please choose one of '.json_encode($supportedDrivers))
                    ->end()
                    ->defaultValue('orm')
                    ->cannotBeOverwritten()
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('subscription_db_driver')
                    ->validate()
                        ->ifNotInArray($supportedMessageDrivers)
                        ->thenInvalid('The driver %s is not supported. Please choose one of '.json_encode($supportedDrivers))
                    ->end()
                    ->defaultValue('orm')
                    ->cannotBeOverwritten()
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('message_model_manager_name')->defaultNull()->end()
                ->scalarNode('filter_model_manager_name')->defaultNull()->end()
                ->scalarNode('subscription_model_manager_name')->defaultNull()->end()
                ->booleanNode('use_log_handler')->defaultValue(false)->end()
                ->scalarNode('elastica_index_name')->defaultNull()->end()
                ->scalarNode('elastica_type_name')->defaultValue('message')->end()
            ->end()
            // Using the custom driver requires changing the manager services
            ->validate()
                ->ifTrue(function ($v) {return 'custom' === $v['message_db_driver'] && 'phlexible_message.message_manager.default' === $v['service']['message_manager'];})
                ->thenInvalid('You need to specify your own message manager service when using the "custom" driver.')
            ->end()
            ->validate()
                ->ifTrue(function ($v) {return 'custom' === $v['filter_db_driver'] && 'phlexible_message.filter_manager.default' === $v['service']['filter_manager'];})
                ->thenInvalid('You need to specify your own filter manager service when using the "custom" driver.')
            ->end()
            ->validate()
                ->ifTrue(function ($v) {return 'custom' === $v['subscription_db_driver'] && 'phlexible_message.subscription_manager.default' === $v['service']['subscription_manager'];})
                ->thenInvalid('You need to specify your own subscription manager service when using the "custom" driver.')
            ->end()
            ->validate()
                ->ifTrue(function ($v) {return 'elastica' === $v['message_db_driver'] && null === $v['elastica_index_name'];})
                ->thenInvalid('You need to specify the elastica_index_name for the "elastica" driver.')
            ->end()
            ->validate()
                ->ifTrue(function ($v) {return 'elastica' === $v['message_db_driver'] && null === $v['elastica_type_name'];})
                ->thenInvalid('You need to specify the elastica_type_name for the "elastica" driver.')
            ->end();

        $this->addServiceSection($rootNode);

        return $treeBuilder;
    }

    /**
     * @param ArrayNodeDefinition $node
     */
    private function addServiceSection(ArrayNodeDefinition $node)
    {
        $node
            ->addDefaultsIfNotSet()
            ->children()
                ->arrayNode('service')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('message_manager')->defaultValue('phlexible_message.message_manager.default')->end()
                        ->scalarNode('filter_manager')->defaultValue('phlexible_message.filter_manager.default')->end()
                        ->scalarNode('subscription_manager')->defaultValue('phlexible_message.subscription_manager.default')->end()
                    ->end()
                ->end()
            ->end();
    }
}
