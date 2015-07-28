<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\ElementVersion\Diff;

use cogpowered\FineDiff\Diff;
use cogpowered\FineDiff\Granularity\Word;
use Phlexible\Bundle\ElementBundle\Proxy\ChildStructureInterface;

/**
 * Diff result
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class DiffResult
{
    /**
     * @param ChildStructureInterface $structure
     */
    private function applyAdded(ChildStructureInterface $structure)
    {
        $structure
            ->setAttribute('diff', 'added');

        foreach ($structure->getValues() as $value) {
            //$this->applyAddedValue($value);
        }

        foreach ($structure->getStructures() as $childStructure) {
            $this->applyAdded($structure);
        }
    }

    /**
     * @param mixed $value
     */
    private function applyAddedValue($value)
    {
        $value
            ->setAttribute('diff', 'added')
            ->setAttribute('oldValue', '');
    }

    /**
     * @param mixed $value
     * @param mixed $oldValue
     */
    private function applyModifiedValue($value, $oldValue)
    {
        $granularity = new Word;
        $diff = new Diff($granularity);

        $value
            ->setAttribute('diff', 'modified')
            ->setAttribute('oldValue', $oldValue)
            ->setAttribute('diffValue', $diff->render($oldValue, $value->getValue()));
    }

    /**
     * @param ChildStructureInterface $structure
     */
    private function applyRemoved(ChildStructureInterface $structure)
    {
        $structure
            ->setAttribute('diff', 'removed');

        foreach ($structure->getValues() as $value) {
            //$this->applyRemovedValue($value);
        }

        foreach ($structure->getStructures() as $childStructure) {
            $this->applyRemoved($structure);
        }
    }

    /**
     * @param mixed $value
     */
    private function applyRemovedValue($value)
    {
        $value
            ->setAttribute('diff', 'removed')
            ->setAttribute('oldValue', $value->getValue())
            //->setValue('')
        ;
    }
}