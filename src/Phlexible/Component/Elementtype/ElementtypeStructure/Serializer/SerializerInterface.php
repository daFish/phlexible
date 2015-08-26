<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\Elementtype\ElementtypeStructure\Serializer;

use Phlexible\Component\Elementtype\Domain\ElementtypeStructure;

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
     * @param ElementtypeStructure $elementtypeStructure
     *
     * @return string
     */
    public function serialize(ElementtypeStructure $elementtypeStructure);
}
