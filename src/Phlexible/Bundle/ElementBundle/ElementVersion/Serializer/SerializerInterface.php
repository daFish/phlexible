<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\ElementVersion\Serializer;

use Phlexible\Bundle\ElementBundle\Entity\ElementVersion;

/**
 * Serializer interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface SerializerInterface
{
    /**
     * Serialize structure
     *
     * @param ElementVersion $elementVersion
     * @param string         $language
     *
     * @return string
     */
    public function serialize(ElementVersion $elementVersion, $language);
}
