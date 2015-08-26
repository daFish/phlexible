<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\Elementtype\Field;

/**
 * Link field
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class LinkField extends AbstractField
{
    /**
     * {@inheritdoc}
     */
    public function getIcon()
    {
        return 'p-elementtype-field_link-icon';
    }

    /**
     * {@inheritdoc}
     */
    public function getDataType()
    {
        return 'array';
    }
}
