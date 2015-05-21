<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\Elementtype\Field\Container;

/**
 * Root container
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class RootContainer extends AbstractContainer
{
    /**
     * {@inheritdoc}
     */
    public function getIcon()
    {
        return 'p-elementtype-container_root-icon';
    }
}