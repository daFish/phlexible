<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\Elementtype\ElementtypeStructure\Diff;

use Phlexible\Component\Elementtype\Domain\ElementtypeStructure;
use Phlexible\Component\Elementtype\Domain\ElementtypeStructureNode;

/**
 * Differ
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class Differ
{
    /**
     * @param \Phlexible\Component\Elementtype\Domain\ElementtypeStructure $fromStructure
     * @param ElementtypeStructure $toStructure
     *
     * @return Diff
     */
    public function diff(ElementtypeStructure $fromStructure, ElementtypeStructure $toStructure)
    {
        $diff = new Diff();

        $this->doDiff($diff, $fromStructure, $toStructure);

        return $diff;
    }

    /**
     * @param Diff                 $diff
     * @param \Phlexible\Component\Elementtype\Domain\ElementtypeStructure $fromStructure
     * @param \Phlexible\Component\Elementtype\Domain\ElementtypeStructure $toStructure
     *
     * @return Diff
     */
    private function doDiff(Diff $diff, ElementtypeStructure $fromStructure = null, ElementtypeStructure $toStructure = null)
    {
        $rii = new \RecursiveIteratorIterator($fromStructure->getIterator(), \RecursiveIteratorIterator::SELF_FIRST);
        foreach ($rii as $fromNode) {
            /* @var $fromNode ElementtypeStructureNode */

            if ($toNode = $toStructure->getNode($fromNode->getDsId())) {
                if (implode('_', $toNode->getRepeatableDsIdPath()) !== implode('_', $fromNode->getRepeatableDsIdPath())) {
                    $diff->addMoved($fromNode, $toNode);
                }
            } else {
                $diff->addRemoved($fromNode);
            }
        }

        $rii = new \RecursiveIteratorIterator($toStructure->getIterator(), \RecursiveIteratorIterator::SELF_FIRST);
        foreach ($rii as $toNode) {
            /* @var $toNode \Phlexible\Component\Elementtype\Domain\ElementtypeStructureNode */

            if (!$fromStructure->getNode($toNode->getDsId())) {
                $diff->addAdded($toNode);
            }
        }

        return $diff;
    }
}
