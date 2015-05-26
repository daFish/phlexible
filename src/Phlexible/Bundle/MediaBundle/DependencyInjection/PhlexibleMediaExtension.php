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
        $loader->load('mime_sniffer.yml');
        $loader->load('ffmpeg.yml');
        $loader->load('swftools.yml');
        $loader->load('poppler.yml');
        $loader->load('exiftool.yml');
        $loader->load('image_analyzer.yml');
        $loader->load('media_classifier.yml');
        $loader->load('media_converter.yml');
        $loader->load('meta_reader.yml');
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
            'ffprobe.binaries' => $config['ffmpeg']['ffprobe'],
            'ffmpeg.binaries'  => $config['ffmpeg']['ffmpeg'],
        ));

        $container->setParameter('phlexible_media.mime_sniffer.file', $config['mime_sniffer']['file']);
        $container->setParameter('phlexible_media.mime_sniffer.magicfile', $config['mime_sniffer']['magicfile']);
        $container->setAlias('phlexible_media.mime_sniffer.adapter', $config['mime_sniffer']['adapter']);

        $container->setAlias('phlexible_media.image_analyzer.driver', 'phlexible_media.image_analyzer.driver.' . $config['image_analyzer']['driver']);

        $container->setAlias('phlexible_media.imagine', 'phlexible_media.imagine.' . $config['imagine']['driver']);
    }
}
