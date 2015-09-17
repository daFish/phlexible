<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\ElementProxy\Distiller;

use Phlexible\Component\Elementtype\Domain\Elementtype;
use Phlexible\Component\Elementtype\Domain\ElementtypeStructure;
use Phlexible\Component\Elementtype\Domain\ElementtypeStructureNode;
use Phlexible\Component\Elementtype\Field\FieldRegistry;

/**
 * Distiller.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class Distiller
{
    /**
     * @var FieldRegistry
     */
    private $fieldRegistry;

    /**
     * @param FieldRegistry $fieldRegistry
     */
    public function __construct(FieldRegistry $fieldRegistry)
    {
        $this->fieldRegistry = $fieldRegistry;
    }

    /**
     * @param Elementtype $elementtype
     *
     * @return DistilledNodeCollection
     */
    public function distill(Elementtype $elementtype)
    {
        $elementtypeStructure = $elementtype->getStructure();
        $rootNode = $elementtypeStructure->getRootNode();

        return $this->iterate($elementtypeStructure, $rootNode);
    }

    /**
     * @param ElementtypeStructure     $structure
     * @param ElementtypeStructureNode $node
     * @param int                      $depth
     *
     * @return DistilledNodeCollection
     */
    private function iterate(ElementtypeStructure $structure, ElementtypeStructureNode $node, $depth = 0)
    {
        $nodes = array();

        foreach ($structure->getChildNodes($node->getDsId()) as $childNode) {
            $field = $this->fieldRegistry->getField($childNode->getType());

            if ($field->isField()) {
                $nodes[] = new DistilledFieldNode($childNode, $field);
            }

            if ($structure->hasChildNodes($childNode->getDsId())) {
                $childNodes = $this->iterate($structure, $childNode, $depth + 1);

                if ($childNode->isRepeatable() || $childNode->isOptional()) {
                    $nodes[] = new DistilledContainerNode($childNode, $childNodes);
                } else {
                    $nodes = array_merge($nodes, $childNodes->all());
                }
            }
        }

        return new DistilledNodeCollection($nodes);
    }
}
