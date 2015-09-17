<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\GuiBundle\Event;

use Phlexible\Bundle\GuiBundle\Poller\MessageCollection;
use Symfony\Component\EventDispatcher\Event;

/**
 * Poll event.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class PollEvent extends Event
{
    /**
     * @var MessageCollection
     */
    private $messages;

    /**
     * @param MessageCollection $messages
     */
    public function __construct(MessageCollection $messages)
    {
        $this->messages = $messages;
    }

    /**
     * @return MessageCollection
     */
    public function getMessages()
    {
        return $this->messages;
    }
}
