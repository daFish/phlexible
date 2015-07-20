<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Proxy;

use Phlexible\Component\Elementtype\Field\FieldRegistry;
use Phlexible\Component\Elementtype\Model\Elementtype;
use Phlexible\Component\Elementtype\Model\ElementtypeStructure;
use Phlexible\Component\Elementtype\Model\ElementtypeStructureNode;

/**
 * Distiller
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
     * {@inheritdoc}
     */
    public function distill(Elementtype $elementtype)
    {
        $elementtypeStructure = $elementtype->getStructure();

        $rootNode = $elementtypeStructure->getRootNode();
        $data = $this->iterate($elementtypeStructure, $rootNode);

        return $data;
    }

    private function iterate(ElementtypeStructure $structure, ElementtypeStructureNode $node, $depth = 0)
    {
        $data = array();

        foreach ($structure->getChildNodes($node->getDsId()) as $childNode) {
            $field = $this->fieldRegistry->getField($childNode->getType());

            if ($field->isField()) {
                $data[] = array(
                    'name'  => $childNode->getName(),
                    'node'  => $childNode,
                    'field' => $field,
                );
            }

            if ($structure->hasChildNodes($childNode->getDsId())) {
                $childData = $this->iterate($structure, $childNode, $depth + 1);

                if ($childNode->isRepeatable() || $childNode->isOptional()) {
                    $data[] = array(
                        'name'     => $childNode->getName(),
                        'node'     => $childNode,
                        'field'    => $field,
                        'children' => $childData
                    );
                } else {
                    $data = array_merge($data, $childData);
                }
            }
        }

        return $data;
    }
}
