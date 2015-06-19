<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MetaSetBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Add option resolvers pass
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class AddOptionResolversPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $optionResolvers = [];
        foreach (array_keys($container->findTaggedServiceIds('phlexible_meta_set.option_resolver')) as $bla => $id) {
            print_r($bla);
            print_r($id);
            die;
            $optionResolvers[$type] = new Reference($id);
        }
        $container->getDefinition('phlexible_meta_set.option_resolver')->replaceArgument(0, $optionResolvers);
    }
}
