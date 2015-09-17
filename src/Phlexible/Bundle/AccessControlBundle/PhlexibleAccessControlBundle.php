<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\AccessControlBundle;

use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;
use Phlexible\Bundle\AccessControlBundle\DependencyInjection\Compiler\AddPermissionsPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Access control bundle
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class PhlexibleAccessControlBundle extends Bundle
{
    const RESOURCE_ACCESS_CONTROL = 'accesscontrol';

    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new AddPermissionsPass());

        $container->addCompilerPass(
            DoctrineOrmMappingsPass::createXmlMappingDriver(
                array(
                    $this->getPath().'/Resources/config/doctrine/model' => 'Phlexible\Component\AccessControl\Domain',
                ),
                array(null),
                'phlexible_access_control.backend_type_orm',
                array('PhlexibleAccessControlBundle' => 'Phlexible\Component\AccessControl\Domain')
            )
        );
    }
}
