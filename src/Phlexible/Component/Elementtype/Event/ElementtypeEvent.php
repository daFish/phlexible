<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\Elementtype\Event;

use Phlexible\Component\Elementtype\Domain\Elementtype;
use Symfony\Component\EventDispatcher\Event;

/**
 * Elementtype event.
 *
 * @author Phillip Look <pl@brainbits.net>
 */
class ElementtypeEvent extends Event
{
    /**
     * @var \Phlexible\Component\Elementtype\Domain\Elementtype
     */
    private $elementtype;

    /**
     * @param Elementtype $elementtype
     */
    public function __construct(Elementtype $elementtype)
    {
        $this->elementtype = $elementtype;
    }

    /**
     * @return Elementtype
     */
    public function getElementtype()
    {
        return $this->elementtype;
    }
}
