<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementBundle\Change;

/**
 * Elementtype change.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface ChangeInterface
{
    /**
     * @return string
     */
    public function getReason();
}
