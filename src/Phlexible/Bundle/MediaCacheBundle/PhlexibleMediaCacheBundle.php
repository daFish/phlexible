<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\MediaCacheBundle;

use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;
use Phlexible\Bundle\MediaCacheBundle\DependencyInjection\Compiler\AddSpecifiersPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Media cache bundle
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class PhlexibleMediaCacheBundle extends Bundle
{
    const ROLE_MEDIA_CACHE = 'ROLE_MEDIA_CACHE';

    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new AddSpecifiersPass());

        $container->addCompilerPass(
            DoctrineOrmMappingsPass::createXmlMappingDriver(
                array($this->getPath().'/Resources/config/orm' => 'Phlexible\Component\MediaCache\Domain'),
                array('phlexible_media_cache.model_manager_name'),
                'phlexible_media_cache.backend_type_orm'
            )
        );
    }
}
