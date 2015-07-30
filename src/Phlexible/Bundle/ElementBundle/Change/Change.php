<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Change;

use Phlexible\Component\Elementtype\Domain\Elementtype;
use Phlexible\Component\Elementtype\Usage\Usage;

/**
 * Elementtype change
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
abstract class Change implements ChangeInterface
{
    /**
     * @var \Phlexible\Component\Elementtype\Domain\Elementtype
     */
    private $elementtype;

    /**
     * @var Usage[]
     */
    private $usage;

    /**
     * @param \Phlexible\Component\Elementtype\Domain\Elementtype $elementtype
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
