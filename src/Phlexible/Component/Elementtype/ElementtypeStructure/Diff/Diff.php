<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\Elementtype\ElementtypeStructure\Diff;

use Phlexible\Component\Elementtype\Domain\ElementtypeStructureNode;

/**
 * Diff.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class Diff
{
    private $added = array();
    private $moved = array();
    private $removed = array();

    /**
     * @param \Phlexible\Component\Elementtype\Domain\ElementtypeStructureNode $newNode
     *
     * @return $this
     */
    public function addAdded(ElementtypeStructureNode $newNode)
    {
        $this->added[] = $newNode;

        return $this;
    }

    /**
     * @return array
     */
    public function getAdded()
    {
        return $this->added;
    }

    /**
     * @param \Phlexible\Component\Elementtype\Domain\ElementtypeStructureNode $oldNode
     * @param \Phlexible\Component\Elementtype\Domain\ElementtypeStructureNode $newNode
     *
     * @return $this
     */
    public function addMoved(ElementtypeStructureNode $oldNode, ElementtypeStructureNode $newNode)
    {
        $this->moved[] = array('oldNode' => $oldNode, 'newNode' => $newNode);

        return $this;
    }

    /**
     * @return array
     */
    public function getMoved()
    {
        return $this->moved;
    }

    /**
     * @param \Phlexible\Component\Elementtype\Domain\ElementtypeStructureNode $oldNode
     *
     * @return $this
     */
    public function addRemoved(ElementtypeStructureNode $oldNode)
    {
        $this->removed[] = $oldNode;

        return $this;
    }

    /**
     * @return array
     */
    public function getRemoved()
    {
        return $this->removed;
    }
}
