<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\ElementProxy;

/**
 * Main structure interface.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface MainStructureInterface extends StructureInterface
{
    /**
     * @return string
     */
    public function __id();

    /**
     * @return string
     */
    public function __version();

    /**
     * @return string
     */
    public function __elementtypeId();

    /**
     * @return int
     */
    public function __elementtypeRevision();

    /**
     * @return string
     */
    public function __elementtypeName();
}
