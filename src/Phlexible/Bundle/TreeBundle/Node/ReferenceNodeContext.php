<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\TreeBundle\Node;

use Phlexible\Bundle\TreeBundle\Mediator\MediatorInterface;
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
     * @param NodeInterface     $node
     * @param TreeInterface     $tree
     * @param MediatorInterface $mediator
     * @param string            $language
     * @param NodeContext       $referenceNode
     * @param int               $maxDepth
     * @param int               $depth
     */
    public function __construct(
        NodeInterface $node,
        TreeInterface $tree,
        MediatorInterface $mediator,
        $language,
        NodeContext $referenceNode = null,
        $maxDepth = null,
        $depth = 0)
    {
        parent::__construct($node, $tree, $mediator, $language);

        $this->referenceNode = $referenceNode;
        $this->maxDepth = $maxDepth;
        $this->depth = $depth;
    }

    /**
     * @param NodeContext $node
     * @param NodeContext $referenceNode
     * @param int         $maxDepth
     * @param int         $depth
     *
     * @return ReferenceNodeContext
     */
    public static function fromNodeContext(NodeContext $node, NodeContext $referenceNode = null, $maxDepth = null, $depth = 0)
    {
        return new self(
            $node->getNode(),
            $node->getTree(),
            $node->getMediator(),
            $node->getLanguage(),
            $referenceNode,
            $maxDepth,
            $depth
        );
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
                $children[] = self::fromNodeContext(
                    $childNode,
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
                $children[] = self::fromNodeContext(
                    $childNode,
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

        return self::fromNodeContext(
            $this->getParent(),
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
                $children[] = self::fromNodeContext(
                    $childNode,
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
