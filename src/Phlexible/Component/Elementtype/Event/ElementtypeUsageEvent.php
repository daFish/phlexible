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
use Phlexible\Component\Elementtype\Usage\Usage;

/**
 * Elementtype event.
 *
 * @author Phillip Look <pl@brainbits.net>
 */
class ElementtypeUsageEvent extends ElementtypeEvent
{
    /**
     * @var \Phlexible\Component\Elementtype\Usage\Usage[]
     */
    private $usage = array();

    /**
     * @param \Phlexible\Component\Elementtype\Domain\Elementtype $elementtype
     */
    public function __construct(Elementtype $elementtype)
    {
        parent::__construct($elementtype);
    }

    /**
     * @param \Phlexible\Component\Elementtype\Usage\Usage $usage
     */
    public function addUsage(Usage $usage)
    {
        $this->usage[$usage->getId()] = $usage;
    }

    /**
     * @return \Phlexible\Component\Elementtype\Usage\Usage[]
     */
    public function getUsage()
    {
        return $this->usage;
    }
}
