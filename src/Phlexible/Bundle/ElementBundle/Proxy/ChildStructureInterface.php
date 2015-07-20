<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Proxy;

/**
 * Child structure interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface ChildStructureInterface extends StructureInterface
{
    /**
     * @return string
     */
    public function __id();

    /**
     * @return string
     */
    public function __name();

    /**
     * @return string
     */
    public function __dsId();
}
