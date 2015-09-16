<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\MessageBundle;

use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Message bundle
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class PhlexibleMessageBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(
            DoctrineOrmMappingsPass::createXmlMappingDriver(
                array($this->getPath().'/Resources/config/orm-message' => 'Phlexible\Component\Message\Domain'),
                array('phlexible_message.message_model_manager_name'),
                'phlexible_message.message_backend_type_orm'
            )
        );
        $container->addCompilerPass(
            DoctrineOrmMappingsPass::createXmlMappingDriver(
                array($this->getPath().'/Resources/config/orm-filter' => 'Phlexible\Component\MessageFilter\Domain'),
                array('phlexible_message.filter_model_manager_name'),
                'phlexible_message.filter_backend_type_orm'
            )
        );
        $container->addCompilerPass(
            DoctrineOrmMappingsPass::createXmlMappingDriver(
                array($this->getPath().'/Resources/config/orm-subscription' => 'Phlexible\Component\MessageSubscription\Domain'),
                array('phlexible_message.subscription_model_manager_name'),
                'phlexible_message.subscription_backend_type_orm'
            )
        );
    }
}
