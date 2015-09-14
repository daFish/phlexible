<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\Tree;

/**
 * Tree context interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface TreeContextInterface
{
    /**
     * @return string
     */
    public function getWorkspace();

    /**
     * @return string
     */
    public function getLocale();
}
