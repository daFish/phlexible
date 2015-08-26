<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\TreeBundle;

use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;
use Phlexible\Bundle\TreeBundle\DependencyInjection\Compiler\AddFieldMappersPass;
use Symfony\Cmf\Component\Routing\DependencyInjection\Compiler\RegisterRouteEnhancersPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Tree bundle
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class PhlexibleTreeBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        $modelDir = realpath(__DIR__.'/Resources/config/doctrine');
        $mappings = array(
            $modelDir => 'Phlexible\Component\Node\Domain',
        );

        $container->addCompilerPass(
            DoctrineOrmMappingsPass::createXmlMappingDriver(
                $mappings,
                array(null),
                'phlexible_tree.backend_type_orm',
                array('PhlexibleNode' => 'Phlexible\Component\Node\Domain')
            )
        );

        $container->addCompilerPass(
            DoctrineOrmMappingsPass::createAnnotationMappingDriver(
                array('Phlexible\Bundle\TreeBundle\Entity'),
                array(realpath(__DIR__.'/Entity')),
                array(null),
                'phlexible_tree.backend_type_orm',
                array('PhlexibleTreeBundle' => 'Phlexible\Bundle\TreeBundle\Entity')
            )
        );

        $container->addCompilerPass(new AddFieldMappersPass());
        $container->addCompilerPass($this->buildMappingCompilerPass());
        $container->addCompilerPass(
            new RegisterRouteEnhancersPass('phlexible_tree.router', 'phlexible_tree.route_enhancer')
        );
    }

    /**
     * @return DoctrineOrmMappingsPass
     */
    private function buildMappingCompilerPass()
    {
        $arguments = array(array(realpath(__DIR__ . '/Resources/config/doctrine-base')), '.orm.xml');
        $locator = new Definition('Doctrine\Common\Persistence\Mapping\Driver\DefaultFileLocator', $arguments);
        $driver = new Definition('Doctrine\ORM\Mapping\Driver\XmlDriver', array($locator));

        return new DoctrineOrmMappingsPass(
            $driver,
            array('Symfony\Component\Routing\Route'),
            array('phlexible_siteroot.model_manager_name'),
            'phlexible_tree.backend_type_orm'
        );
    }
}
