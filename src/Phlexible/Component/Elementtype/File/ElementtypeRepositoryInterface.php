<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\Elementtype\File;

use Phlexible\Component\Elementtype\Domain\Elementtype;

/**
 * Elementtype repository interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface ElementtypeRepositoryInterface
{
    /**
     * @return \Phlexible\Component\Elementtype\Domain\Elementtype[]
     */
    public function loadAll();

    /**
     * @param string $elementtypeId
     *
     * @return \Phlexible\Component\Elementtype\Domain\Elementtype
     */
    public function load($elementtypeId);

    /**
     * @param \Phlexible\Component\Elementtype\Domain\Elementtype $elementtype
     */
    public function write(Elementtype $elementtype);
}
