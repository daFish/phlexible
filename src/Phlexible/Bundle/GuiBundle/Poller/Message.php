<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\GuiBundle\Poller;

use DateTime;
use DateTimeInterface;

/**
 * Poller message.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class Message
{
    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $event;

    /**
     * @var mixed
     */
    private $data;

    /**
     * @var DateTimeInterface
     */
    private $timestamp;

    /**
     * @param string $type
     * @param string $event
     * @param mixed  $data
     */
    public function __construct($type, $event, $data)
    {
        $this->type = $type;
        $this->event = $event;
        $this->data = $data;

        $this->timestamp = new DateTime();
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return DateTime
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }
}
