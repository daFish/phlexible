<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\AccessControl;

/**
 * Access control events
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class AccessControlEvents
{
    /**
     * Before set right event
     * Called before setting a right
     */
    const BEFORE_SET_RIGHT = 'phlexible_access_control.before_set_right';

    /**
     * Set right event
     * Called after setting a right
     */
    const SET_RIGHT = 'phlexible_access_control.set_right';

    /**
     * Before remove right event
     * Called before removing a right
     */
    const BEFORE_REMOVE_RIGHT = 'phlexible_access_control.before_remove_right';

    /**
     * Remove right event
     * Called after removing a right
     */
    const REMOVE_RIGHT = 'phlexible_access_control.remove_right';
}
