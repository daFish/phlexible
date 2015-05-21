<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\CmsBundle\Field;

use Phlexible\Component\Elementtype\Field\AbstractField;

/**
 * Folder field
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class FolderField extends AbstractField
{
    /**
     * {@inheritdoc}
     */
    public function getIcon()
    {
        return 'p-cms-field_folder-icon';
    }

    /**
     * {@inheritdoc}
     */
    public function getDataType()
    {
        return 'string';
    }
}
