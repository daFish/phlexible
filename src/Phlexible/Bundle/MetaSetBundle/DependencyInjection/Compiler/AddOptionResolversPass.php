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
        $optionResolvers = array();
        foreach ($container->findTaggedServiceIds('phlexible_meta_set.option_resolver') as $id => $attributes) {
            if (!isset($attributes[0]['type'])) {
                throw new \InvalidArgumentException("attribute type must be set on phlexible_meta_set.option_resolver");
            }
            $type = $attributes[0]['type'];
            $optionResolvers[$type] = new Reference($id);
        }
        $container->getDefinition('phlexible_meta_set.option_resolver')->replaceArgument(0, $optionResolvers);
    }
}
