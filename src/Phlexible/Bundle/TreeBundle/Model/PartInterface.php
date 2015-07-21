<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\Model;

/**
 * Part interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface PartInterface
{
    /**
     * @return string
     */
    public function getAreaId();

    /**
     * @param string $areaId
     *
     * @return $this
     */
    public function setAreaId($areaId);
}
