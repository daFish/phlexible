<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementBundle\Lock;

/**
 * Lock identity.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface LockIdentityInterface
{
    /**
     * Return string representation of this lock identity.
     *
     * @return string
     */
    public function __toString();
}
