<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Proxy\Distiller;

use Phlexible\Component\Elementtype\Domain\ElementtypeStructureNode;

/**
 * Distilled node interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface DistilledNodeInterface
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getType();

    /**
     * @return string
     */
    public function getDsId();

    /**
     * @return ElementtypeStructureNode
     */
    public function getParentNode();
}
