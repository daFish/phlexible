<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\Suggest;

/**
 * Suggest events.
 *
 * @author Phillip Look <pl@brainbits.net>
 */
class SuggestEvents
{
    /**
     * Fired before garbage collection is invoked.
     */
    const BEFORE_GARBAGE_COLLECT = 'phlexible_data_source.before_garbage_collect';

    /**
     * Fired after garbage collection is invoked.
     */
    const GARBAGE_COLLECT = 'phlexible_data_source.garbage_collect';
}
