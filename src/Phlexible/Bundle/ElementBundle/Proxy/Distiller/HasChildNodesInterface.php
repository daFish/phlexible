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
