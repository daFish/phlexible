<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\Node;

use Phlexible\Bundle\TreeBundle\Model\TreeInterface;
use Phlexible\Component\Node\Model\NodeInterface;

/**
 * Node context
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ReferenceNodeContext extends NodeContext
{
    /**
     * @var NodeContext
     */
    private $referenceNode;

    /**
     * @var int
     */
    private $maxDepth;

    /**
     * @var int
     */
    private $depth;

    /**
     * @param NodeInterface $node
     * @param TreeInterface $tree
     * @param string        $language
     * @param NodeContext   $referenceNode
     * @param int           $maxDepth
     * @param int           $depth
     */
    public function __construct(
        NodeInterface $node,
        TreeInterface $tree,
        $language,
        NodeContext $referenceNode = null,
        $maxDepth = null,
        $depth = 0)
    {
        parent::__construct($node, $tree, $language);

        $this->referenceNode = $referenceNode;
        $this->maxDepth = $maxDepth;
        $this->depth = $depth;
    }

    /**
     * @return NodeContext[]
     */
    public function getBefore()
    {
        $parentNode = $this->getParent();

        $children = array();
        $keep = true;
        foreach ($this->tree->getChildren($parentNode) as $childNode) {
            if ($childNode->getId() === $this->node->getId()) {
                $keep = false;
                continue;
            }
            if ($keep && $childNode->isAvailable()) {
                $children[] = new self(
                    $childNode->getNode(),
                    $this->tree,
                    $this->language,
                    $this->referenceNode,
                    $this->maxDepth,
                    $this->depth + 1
                );
            }
        }

        return $children;
    }

    /**
     * @return NodeContext[]
     */
    public function getAfter()
    {
        $parentNode = $this->getParent();

        $children = array();
        $keep = false;
        foreach ($this->tree->getChildren($parentNode) as $childNode) {
            if ($childNode->getId() === $this->node->getId()) {
                $keep = true;
                continue;
            }
            if ($keep && $childNode->isAvailable()) {
                $children[] = new self(
                    $childNode->getNode(),
                    $this->tree,
                    $this->language,
                    $this->referenceNode,
                    $this->maxDepth,
                    $this->depth + 1
                );
            }
        }

        return $children;
    }

    /**
     * @return NodeContext|null
     */
    public function getParent()
    {
        $parentNode = $this->getParent();
        if (!$parentNode) {
            return null;
        }

        return new self(
            $this->tree->get($parentNode->getId())->getNode(),
            $this->tree,
            $this->language,
            $this->referenceNode,
            $this->maxDepth,
            $this->depth - 1
        );
    }

    /**
     * @return NodeContext[]
     */
    public function getChildren()
    {
        if ($this->maxDepth && $this->depth >= $this->maxDepth) {
            return array();
        }

        $children = array();
        foreach ($this->tree->getChildren($this) as $childNode) {
            if ($childNode->isAvailable()) {
                $children[] = new self(
                    $childNode->getNode(),
                    $this->tree,
                    $this->language,
                    $this->referenceNode,
                    $this->maxDepth,
                    $this->depth + 1
                );
            }
        }

        return $children;
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        if (!$this->referenceNode) {
            return false;
        }

        if ($this->node->getId() === $this->referenceNode->getId()) {
            return true;
        }

        return $this->tree->isParentOf($this, $this->referenceNode);
    }
}
