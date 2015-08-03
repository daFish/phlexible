<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Proxy\Distiller;

use Phlexible\Component\Elementtype\Domain\ElementtypeStructureNode;
use Phlexible\Component\Elementtype\Field\AbstractField;

/**
 * Distilled field node
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
