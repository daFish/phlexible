<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\SiterootBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Siteroot extension
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class PhlexibleSiterootExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');

        $configuration = $this->getConfiguration($config, $container);
        $config = $this->processConfiguration($configuration, $config);

        if ('custom' !== $config['db_driver']) {
            $loader->load(sprintf('%s.yml', $config['db_driver']));
            $container->setParameter($this->getAlias() . '.backend_type_' . $config['db_driver'], true);
        }

        if (!empty($config['mappings'])) {
            $mappings = [];
            foreach ($config['mappings'] as $mappedUrl => $siterootUrl) {
                $mappings[$mappedUrl] = $siterootUrl;
            }
            $container->setParameter('phlexible_siteroot.mappings', $mappings);
        }

        $container->setParameter('phlexible_siteroot.model_manager_name', $config['model_manager_name']);
        $container->setAlias('phlexible_siteroot.siteroot_manager', $config['service']['siteroot_manager']);
    }
}
