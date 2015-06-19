<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MetaSetBundle;

use Phlexible\Bundle\MetaSetBundle\DependencyInjection\Compiler\AddOptionResolversPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Meta set bundle
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class PhlexibleMetaSetBundle extends Bundle
{
    const RESOURCE_META_SETS = 'metasets';

    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new AddOptionResolversPass());
    }
}
