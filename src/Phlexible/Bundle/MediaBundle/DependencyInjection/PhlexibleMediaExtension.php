<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Phlexible media extension
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class PhlexibleMediaExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('ffmpeg.yml');
        $loader->load('mp4box.yml');
        $loader->load('swftools.yml');
        $loader->load('poppler.yml');
<<<<<<< HEAD:src/Phlexible/Bundle/MediaBundle/DependencyInjection/PhlexibleMediaExtension.php
        $loader->load('exiftool.yml');
        $loader->load('image_analyzer.yml');
        $loader->load('media_classifier.yml');
        $loader->load('media_converter.yml');
        $loader->load('meta_reader.yml');
=======
        $loader->load('imageanalyzer.yml');
>>>>>>> origin/master:src/Phlexible/Bundle/MediaToolBundle/DependencyInjection/PhlexibleMediaToolExtension.php
        $loader->load('imagine.yml');

        $configuration = $this->getConfiguration($config, $container);
        $config = $this->processConfiguration($configuration, $config);

        $container->setParameter('phlexible_media.swftools.configuration', array(
            'pdf2swf.binaries'    => $config['swftools']['pdf2swf'],
            'swfrender.binaries'  => $config['swftools']['swfrender'],
            'swfextract.binaries' => $config['swftools']['swfextract'],
            'timeout'             => $config['swftools']['timeout']
        ));

        $container->setParameter('phlexible_media.poppler.configuration', array(
            'pdfinfo.binaries'   => $config['poppler']['pdfinfo'],
            'pdftotext.binaries' => $config['poppler']['pdftotext'],
            'pdftohtml.binaries' => $config['poppler']['pdftohtml'],
            'timeout'            => $config['poppler']['timeout'],
        ));

        $container->setParameter('phlexible_media.ffmpeg.configuration', array(
            'ffmpeg.binaries'  => $config['ffmpeg']['ffmpeg'],
            'ffprobe.binaries' => $config['ffmpeg']['ffprobe'],
            'timeout'          => $config['ffmpeg']['timeout']
        ));

        $container->setParameter('phlexible_media.mp4box.configuration', array(
            'mp4box.binaries' => $config['mp4box']['mp4box'],
            'timeout'         => $config['mp4box']['timeout']
        ));

<<<<<<< HEAD:src/Phlexible/Bundle/MediaBundle/DependencyInjection/PhlexibleMediaExtension.php
        $container->setParameter('phlexible_media.media_classifier.file', $config['media_classifier']['file']);
=======
        $container->setAlias('phlexible_media_tool.image_analyzer.driver', $config['image_analyzer']['driver']);
>>>>>>> origin/master:src/Phlexible/Bundle/MediaToolBundle/DependencyInjection/PhlexibleMediaToolExtension.php

        $container->setAlias('phlexible_media.image_analyzer.driver', 'phlexible_media.image_analyzer.driver.' . $config['image_analyzer']['driver']);

<<<<<<< HEAD:src/Phlexible/Bundle/MediaBundle/DependencyInjection/PhlexibleMediaExtension.php
        $container->setAlias('phlexible_media.imagine', 'phlexible_media.imagine.' . $config['imagine']['driver']);
=======
        $container->setAlias('phlexible_media_tool.imagine', $config['imagine']['driver']);
>>>>>>> origin/master:src/Phlexible/Bundle/MediaToolBundle/DependencyInjection/PhlexibleMediaToolExtension.php
    }
}
