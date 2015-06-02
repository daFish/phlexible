<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Media tools configuration
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
        $rootNode = $treeBuilder->root('phlexible_media');

        $rootNode
            ->children()
                ->arrayNode('swftools')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('pdf2swf')->defaultValue('pdf2swf')->end()
                        ->scalarNode('swfrender')->defaultValue('swfrender')->end()
                        ->scalarNode('swfextract')->defaultValue('swfextract')->end()
                        ->scalarNode('timeout')->defaultValue(60)->end()
                    ->end()
                ->end()
                ->arrayNode('poppler')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('pdfinfo')->defaultValue('pdfinfo')->end()
                        ->scalarNode('pdftotext')->defaultValue('pdftotext')->end()
                        ->scalarNode('pdftohtml')->defaultValue('pdftohtml')->end()
                        ->scalarNode('timeout')->defaultValue(60)->end()
                    ->end()
                ->end()
                ->arrayNode('ffmpeg')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('ffprobe')->defaultValue('ffprobe')->end()
                        ->scalarNode('ffmpeg')->defaultValue('ffmpeg')->end()
                        ->scalarNode('timeout')->defaultValue(3600)->end()
                    ->end()
                ->end()
                ->arrayNode('mp4box')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('mp4box')->defaultValue('MP4Box')->end()
                        ->scalarNode('timeout')->defaultValue(60)->end()
                    ->end()
                ->end()
                ->arrayNode('imagine')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('driver')->defaultValue('phlexible_media_tool.imagine.imagick')->end()
                    ->end()
                ->end()
                ->arrayNode('image_analyzer')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('driver')->defaultValue('phlexible_media_tool.image_analyzer.driver.imagick')->end()
                    ->end()
                ->end()
                ->arrayNode('media_classifier')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('file')->defaultValue('@PhlexibleMediaBundle/Resources/mediatypes/all.xml')
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
