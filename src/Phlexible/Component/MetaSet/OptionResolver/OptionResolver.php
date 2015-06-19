<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\MetaSet\OptionResolver;

use Phlexible\Component\MetaSet\Model\MetaSetField;

/**
 * Option resolver
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class OptionResolver implements OptionResolverInterface
{
    /**
     * @var OptionResolverInterface[]
     */
    private $optionResolvers = array();

    /**
     * @param OptionResolverInterface[] $optionResolvers
     */
    public function __construct(array $optionResolvers = array())
    {
        foreach ($optionResolvers as $type => $optionResolver) {
            $this->addOptionResolver($type, $optionResolver);
        }
    }

    /**
     * @param string                  $type
     * @param OptionResolverInterface $optionResolver
     */
    public function addOptionResolver($type, OptionResolverInterface $optionResolver)
    {
        $this->optionResolvers[$type] = $optionResolver;
    }

    /**
     * @param MetaSetField $field
     *
     * @return null|array
     */
    public function resolve(MetaSetField $field)
    {
        $type = $field->getType();

        if (isset($this->optionResolvers[$type])) {
            return $this->optionResolvers[$type]->resolve($field);
        }

        return null;
    }

}
