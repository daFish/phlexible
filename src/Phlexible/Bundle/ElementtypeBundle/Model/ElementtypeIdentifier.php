<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementtypeBundle\Model;

use Phlexible\Component\Identifier\Identifier;

/**
 * Elementtype identifier
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ElementtypeIdentifier extends Identifier
{
    /**
     * @param int $elementTypeId
     */
    public function __construct($elementTypeId)
    {
        parent::__construct($elementTypeId);
    }
}
