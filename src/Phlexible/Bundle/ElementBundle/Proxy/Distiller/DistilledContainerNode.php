<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementBundle\Proxy\Distiller;

use Phlexible\Component\Elementtype\Domain\ElementtypeStructureNode;

/**
 * Distilled container node
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class DistilledContainerNode implements DistilledNodeInterface, HasChildNodesInterface
{
    /**
     * @var ElementtypeStructureNode
     */
    private $node;

    /**
     * @var DistilledNodeCollection
     */
    private $childNodes;

    /**
     * @param ElementtypeStructureNode $node
     * @param DistilledNodeCollection  $childNodes
     */
    public function __construct(ElementtypeStructureNode $node, DistilledNodeCollection $childNodes)
    {
        $this->node = $node;
        $this->childNodes = $childNodes;
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
    public function getParentNode()
    {
        return $this->node->getParentNode();
    }

    /**
     * {@inheritdoc}
     */
    public function getChildNodes()
    {
        return $this->childNodes;
    }

    /**
     * {@inheritdoc}
     */
    public function hasChildNodes()
    {
        return count($this->childNodes) > 0;
    }

    /**
     * {@inheritdoc}
     */
    public function isReferenced()
    {
        return $this->node->isReferenced();
    }

    /**
     * {@inheritdoc}
     */
    public function isRepeatable()
    {
        return $this->node->isRepeatable();
    }
}
