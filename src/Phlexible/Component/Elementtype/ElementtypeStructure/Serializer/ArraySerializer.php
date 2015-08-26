<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\Elementtype\ElementtypeStructure\Serializer;

use Phlexible\Component\Elementtype\Domain\ElementtypeStructure;
use Phlexible\Component\Elementtype\Domain\ElementtypeStructureNode;
use Phlexible\Component\Elementtype\Exception\InvalidArgumentException;

/**
 * Serializer interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ArraySerializer implements SerializerInterface
{
    /**
     * {@inheritdoc}
     */
    public function serialize(ElementtypeStructure $elementtypeStructure)
    {
        if (!$elementtypeStructure->getRootNode()) {
            return null;
        }

        $rii = new \RecursiveIteratorIterator($elementtypeStructure->getIterator(), \RecursiveIteratorIterator::SELF_FIRST);

        $nodaDatas[] = array();

        foreach ($rii as $node) {
            /* @var $node ElementtypeStructureNode */

            $nodeData = $nodeDatas[$node->getDsId()] = new \ArrayObject(
                array(
                    'comment'          => $node->getComment(),
                    'configuration'    => $node->getConfiguration(),
                    'dsId'             => $node->getDsId(),
                    'id'               => md5(serialize($node)),
                    'labels'           => $this->normalizeLabels($node),
                    'name'             => $node->getName(),
                    'parentDsId'       => $node->getParentDsId(),
                    'parentId'         => md5(serialize($node->getParentNode())),
                    'referenceId'      => $node->getReferenceElementtypeId() ? $node->getReferenceElementtypeId() : null,
                    'referenceVersion' => $node->getReferenceElementtypeId() ? 1 : null,
                    'type'             => $node->getType(),
                    'validation'       => $node->getValidation(),
                    'children'         => array()
                ),
                \ArrayObject::ARRAY_AS_PROPS
            );

            if ($node->getParentDsId()) {
                $nodeDatas[$node->getParentDsId()]['children'][] = $nodeData;
            } elseif (!in_array($node->getType(), array('referenceroot', 'reference'))) {
                if (!empty($rootNode)) {
                    throw new InvalidArgumentException('duplicate root: ' . print_r($nodeData, 1));
                }
                $rootNode = $nodeData;
            }
        }

        return array((array) $rootNode);
    }

    /**
     * @param ElementtypeStructureNode $node
     *
     * @return array
     */
    private function normalizeLabels(ElementtypeStructureNode $node)
    {
        $labels = $node->getLabels();

        $labels += array(
            'fieldLabel' => array(),
            'contextHelp' => array(),
            'prefix' => array(),
            'suffix' => array()
        );

        return $labels;
    }
}
