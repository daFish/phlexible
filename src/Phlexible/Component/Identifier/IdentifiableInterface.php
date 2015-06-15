<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\Identifier;

/**
 * Identifiable interface
 *
 * @author Matthias Harmuth <mharmuth@brainbits.net>
 */
interface IdentifiableInterface
{
    /**
     * Return the identifier for this object
     *
     * @return IdentifierInterface
     */
    public function getIdentifier();
}
