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
 * Select option resolver
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class SelectOptionResolver implements OptionResolverInterface
{
    /**
     * @param MetaSetField $field
     *
     * @return null|array
     */
    public function resolve(MetaSetField $field)
    {
        $options = [];
        foreach (explode(',', $field->getOptions()) as $value) {
            $options[] = [$value, $value];
        }

        return $options;
    }

}
