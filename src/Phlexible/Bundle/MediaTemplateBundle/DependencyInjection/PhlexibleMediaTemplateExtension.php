<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaTemplateBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Media template extension
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class PhlexibleMediaTemplateExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
        $loader->load('previewer.yml');

        $loader->load('file.yml');
        $container->setAlias('phlexible_media_template.template_manager', 'phlexible_media_template.file.template_manager');

        $configuration = $this->getConfiguration($config, $container);
        $config = $this->processConfiguration($configuration, $config);

        $container->setParameter('phlexible_media_template.dumper.filesystem_dir', $config['dumper']['filesystem_dir']);
        $container->setParameter('phlexible_media_template.dumper.puli_resource_dir', $config['dumper']['puli_resource_dir']);
        $container->setParameter('phlexible_media_template.dumper.default_type', $config['dumper']['default_type']);

    }
}
