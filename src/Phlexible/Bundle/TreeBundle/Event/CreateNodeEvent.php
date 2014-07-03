<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\Event;

use Phlexible\Bundle\TreeBundle\Events;

/**
 * Create node event
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class CreateNodeEvent extends BeforeCreateNodeEvent
{
    /**
     * @var string
     */
    protected $eventName = Events::CREATE_NODE;
}