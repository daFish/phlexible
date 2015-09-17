<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\Elementtype\File;

use Phlexible\Component\Elementtype\Domain\Elementtype;

/**
 * Elementtype repository interface.
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
