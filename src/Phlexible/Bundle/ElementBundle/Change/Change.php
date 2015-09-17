<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementBundle\Change;

use Phlexible\Component\Elementtype\Domain\Elementtype;
use Phlexible\Component\Elementtype\Usage\Usage;

/**
 * Elementtype change.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
abstract class Change implements ChangeInterface
{
    /**
     * @var Elementtype
     */
    private $elementtype;

    /**
     * @var Usage[]
     */
    private $usage;

    /**
     * @param Elementtype $elementtype
     * @param Usage[]     $usage
     */
    public function __construct(Elementtype $elementtype, array $usage = array())
    {
        $this->elementtype = $elementtype;
        $this->usage = $usage;
    }

    /**
     * @return Elementtype
     */
    public function getElementtype()
    {
        return $this->elementtype;
    }

    /**
     * @return Usage[]
     */
    public function getUsage()
    {
        return $this->usage;
    }
}
