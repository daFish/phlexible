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
 * Option resolver interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface OptionResolverInterface
{
    /**
     * @param MetaSetField $field
     *
     * @return null|array
     */
    public function resolve(MetaSetField $field);
}
