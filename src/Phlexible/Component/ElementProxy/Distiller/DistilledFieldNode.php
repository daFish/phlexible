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

use Phlexible\Component\Elementtype\Domain\ElementtypeStructureNode;
use Phlexible\Component\Elementtype\Field\AbstractField;

/**
 * Distilled field node.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class DistilledFieldNode implements DistilledNodeInterface, HasDataTypeInterface
{
    /**
     * @var ElementtypeStructureNode
     */
    private $node;

    /**
     * @var AbstractField
     */
    private $field;

    /**
     * @param ElementtypeStructureNode $node
     * @param AbstractField            $field
     */
    public function __construct(ElementtypeStructureNode $node, AbstractField $field)
    {
        $this->node = $node;
        $this->field = $field;
    }

    /**
     * {@inheritdoc}
     */
    public function getParentNode()
    {
        return $this->node->getParentNode();
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->node->getName();
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->node->getType();
    }

    /**
     * {@inheritdoc}
     */
    public function getDsId()
    {
        return $this->node->getDsId();
    }

    /**
     * {@inheritdoc}
     */
    public function getDataType()
    {
        return $this->field->getDataType();
    }
}
