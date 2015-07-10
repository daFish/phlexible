<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\DataSourceBundle\OptionResolver;

use Phlexible\Component\MetaSet\Model\MetaSetField;
use Phlexible\Component\MetaSet\OptionResolver\OptionResolverInterface;

/**
 * Suggest option resolver
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class SuggestOptionResolver implements OptionResolverInterface
{
    /**
     * @param MetaSetField $field
     *
     * @return null|array
     */
    public function resolve(MetaSetField $field)
    {
        $dataSourceId = $field->getOptions();
        $options = array(
            'source_id' => $dataSourceId,
        );

        return $options;
    }

}
