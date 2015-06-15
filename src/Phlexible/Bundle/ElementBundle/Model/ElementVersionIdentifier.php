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

use Phlexible\Component\Identifier\Identifier;

/**
 * Element version identifier
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ElementVersionIdentifier extends Identifier
{
    /**
     * @param int $eid
     * @param int $version
     */
    public function __construct($eid, $version)
    {
        parent::__construct($eid, $version);
    }
}
