<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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
