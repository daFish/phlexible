<?php
/**
 * phlexible
 *
 * @copyright 2007 brainbits GmbH (http://www.brainbits.net)
 * @license   http://www.makeweb.de/LICENCE     Dummy Licence
 */

namespace Phlexible\Bundle\DashboardBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Add infobars pass
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class AddInfobarsPass implements CompilerPassInterface
{
    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container)
    {
        $infobars = array();
        foreach ($container->findTaggedServiceIds('phlexible_dashboard.infobar') as $id => $attributes) {
            $infobars[] = new Reference($id);
        }

        $container->getDefinition('phlexible_dashboard.infobars')->replaceArgument(0, $infobars);
    }
}
