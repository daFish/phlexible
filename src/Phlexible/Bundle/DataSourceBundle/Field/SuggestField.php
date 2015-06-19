<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\DataSourceBundle\Field;

use Phlexible\Component\Elementtype\Field\AbstractField;

/**
 * Suggest field
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class SuggestField extends AbstractField
{
    /**
     * {@inheritdoc}
     */
    public function getIcon()
    {
        return 'p-elementtype-field_select-icon';
    }
}
