<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementtypeBundle;

use Phlexible\Bundle\MessageBundle\Entity\Message;

/**
 * Elementtypes message
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ElementtypesMessage extends Message
{
    /**
     * {@inheritdoc}
     */
    public function getDefaults()
    {
        return array(
            'channel' => 'elementtype',
        );
    }
}