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

use Phlexible\Bundle\LockBundle\Lock\LockIdentifier;

/**
 * Element part lock identifier.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ElementPartLockIdentifier extends LockIdentifier
{
    /**
     * @param int $eid
     */
    public function __construct($eid)
    {
        parent::__construct($eid);
    }
}
