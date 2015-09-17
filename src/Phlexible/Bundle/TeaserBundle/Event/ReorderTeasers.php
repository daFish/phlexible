<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Reorder teasers event.
 *
 * @author Peter Fahsel <pfahsel@brainbits.net>
 */
class ReorderTeasersEvent extends \Symfony\Component\EventDispatcher\Event
{
    /**
     * @var string
     */
    protected $_notificationName = Makeweb_Teasers_Event::REORDER_TEASERS;
}
