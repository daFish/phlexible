<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TeaserBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Teaser extension
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class PhlexibleTeaserExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');
        $loader->load('doctrine.yml');
        $loader->load('mediators.yml');

        $container->setAlias('phlexible_teaser.teaser_manager', 'phlexible_teaser.doctrine.teaser_manager');
        $container->setAlias('phlexible_teaser.teaser_service', 'phlexible_teaser.doctrine.teaser_manager');
    }
}
