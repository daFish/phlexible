<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementBundle\Model;

use Phlexible\Bundle\LockBundle\Lock\LockIdentityInterface;
use Phlexible\Component\Identifier\Identifier;

/**
 * Element identifier
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ElementIdentifier extends Identifier implements LockIdentityInterface
{
    /**
     * @param int    $eid
     * @param string $language
     */
    public function __construct($eid, $language = null)
    {
        if (null === $language) {
            // do not use empty language in identifier
            parent::__construct($eid);
        } else {
            parent::__construct($eid, $language);
        }
    }
}
