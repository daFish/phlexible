<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\Tree;

use Phlexible\Bundle\TreeBundle\Tree\Node\TreeNodeInterface;

/**
 * Tree iterator
 *
 * @author Phillip Look <pl@brainbits.net>
 */
class TreeIterator implements \Iterator, \RecursiveIterator
{
    /**
     * Internal iterator to traverse through children of node.
     *
     * @var \Iterator
     */
    private $iterator;

    /**
     * Create a new tree iterator for a tree or a specific node.
     *
     * @param TreeInterface|TreeNodeInterface $tree
     *     TreeInterface: traverse tree from root node
     *     TreeNodeInterface: traverse a subtree
     * @throws \Exception
     */
    public function __construct($tree)
    {
        if ($tree instanceof TreeInterface) {
            /* @var $tree TreeInterface */
            $node = $tree->getRoot();
            $this->iterator = new \ArrayIterator(array($node));
        } elseif ($tree instanceof TreeNodeInterface) {
            /* @var $tree TreeNodeInterface */
            $node = $tree;
            $tree = $tree->getTree();
            $this->iterator = new \ArrayIterator($tree->getChildren($node));
        } else {
            throw new \Exception('Unsupported type ' . get_class($tree) . '.');
        }
    }

    /**
     * Get current node.
     *
     * @return TreeNodeInterface
     */
    public function current()
    {
        // delegate to internal iterator
        return $this->iterator->current();
    }

    /**
     * Get node ID.
     *
     * @return int
     */
    public function key()
    {
        return $this->current()->getId();
    }

    /**
     * Goto next node.
     */
    public function next()
    {
        // delegate to internal iterator
        $this->iterator->next();
    }

    /**
     * Goto first node.
     */
    public function rewind()
    {
        // delegate to internal iterator
        $this->iterator->rewind();
    }

    /**
     * Check if there is a current element after calls to rewind() or next().
     *
     * @return boolean
     */
    public function valid()
    {
        // delegate to internal iterator
        return $this->iterator->valid();
    }

    /**
     * Get Iterator for currents element children.
     *
     * @return $this
     */
    public function getChildren()
    {
        return new self($this->current());
    }

    /**
     * Check if current element has children.
     *
     * @return boolean
     */
    public function hasChildren()
    {
        $node = $this->current();

        return $node->getTree()->hasChildren($node);
    }

}
