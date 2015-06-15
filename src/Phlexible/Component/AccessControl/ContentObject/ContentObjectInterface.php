<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\AccessControl\ContentObject;

/**
 * Content object interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface ContentObjectInterface
{
    /**
     * Return content object identifier
     *
     * @return array
     */
    public function getContentObjectIdentifiers();

    /**
     * Return content object path
     *
     * @return array
     */
    public function getContentObjectPath();
}
