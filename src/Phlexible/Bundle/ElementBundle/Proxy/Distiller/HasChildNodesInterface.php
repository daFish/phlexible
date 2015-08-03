<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Proxy\Distiller;

/**
 * Has child nodes interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface HasChildNodesInterface
{
    /**
     * @return DistilledNodeCollection
     */
    public function getChildNodes();

    /**
     * @return bool
     */
    public function hasChildNodes();

    /**
     * @return bool
     */
    public function isReferenced();

    /**
     * @return bool
     */
    public function isRepeatable();
}
