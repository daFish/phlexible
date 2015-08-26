<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\TreeBundle\Model;

use Phlexible\Bundle\TreeBundle\Exception\InvalidNodeMoveException;
use Phlexible\Bundle\TreeBundle\Node\NodeContext;
use Phlexible\Component\Node\Model\NodeInterface;
use Phlexible\Component\Tree\TreeContextInterface;

/**
 * Tree interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface TreeInterface
{
    const SORT_MODE_TITLE       = 'title';
    const SORT_MODE_CREATEDATE  = 'createdate';
    const SORT_MODE_PUBLISHDATE = 'publishdate';
    const SORT_MODE_CUSTOMDATE  = 'customdate';
    const SORT_MODE_FREE        = 'free';

    const SORT_DIR_ASC  = 'asc';
    const SORT_DIR_DESC = 'desc';

    /**
     * @return TreeContextInterface
     */
    public function getTreeContext();

    /**
     * @return string
     */
    public function getSiterootId();

    /**
     * Return the root node
     *
     * @return NodeInterface
     */
    public function getRoot();

    /**
     * Return a node
     *
     * @param int $id
     *
     * @return NodeContext
     */
    public function get($id);

    /**
     * Return a node
     *
     * @param int $id
     *
     * @return NodeContext
     */
    public function getWorking($id);

    /**
     * Has this tree the given Tree ID?
     *
     * @param int $id
     *
     * @return bool
     */
    public function has($id);

    /**
     * Return child nodes
     *
     * @param NodeContext $node
     *
     * @return NodeContext[]
     */
    public function getChildren(NodeContext $node);

    /**
     * Are child nodes present?
     *
     * @param NodeContext $node
     *
     * @return bool
     */
    public function hasChildren(NodeContext $node);

    /**
     * Return parent node
     *
     * @param NodeContext $node
     *
     * @return NodeContext
     */
    public function getParent(NodeContext $node);

    /**
     * Return ID path array
     *
     * @param NodeContext $node
     *
     * @return array
     */
    public function getIdPath(NodeContext $node);

    /**
     * Return node path array
     *
     * @param NodeContext $node
     *
     * @return NodeContext[]
     */
    public function getPath(NodeContext $node);

    /**
     * Is the given node the root node?
     *
     * @param NodeContext $node
     *
     * @return bool
     */
    public function isRoot(NodeContext $node);

    /**
     * Is childId a child of parentId?
     *
     * @param NodeContext $childNode
     * @param NodeContext $parentNode
     *
     * @return bool
     */
    public function isChildOf(NodeContext $childNode, NodeContext $parentNode);

    /**
     * Is parentId a parent of childId?
     *
     * @param NodeContext $parentNode
     * @param NodeContext $childNode
     *
     * @return bool
     */
    public function isParentOf(NodeContext $parentNode, NodeContext $childNode);

    /**
     * @param NodeContext $node
     */
    public function updateNode(NodeContext $node);

    /**
     * Create a node
     *
     * @param NodeContext $parentNode
     * @param NodeContext $afterNode
     * @param mixed       $contentDocument
     * @param array       $attributes
     * @param string      $userId
     * @param string      $sortMode
     * @param string      $sortDir
     * @param bool        $navigation
     * @param bool        $needAuthentication
     *
     * @return NodeInterface
     */
    public function createNode(
        NodeContext $parentNode,
        NodeContext $afterNode = null,
        $contentDocument,
        array $attributes,
        $userId,
        $sortMode = 'free',
        $sortDir = 'asc',
        $navigation = false,
        $needAuthentication = false
    );

    /**
     * @param NodeContext $parentNode
     * @param NodeContext $afterNode
     * @param NodeContext $sourceNode
     * @param string      $userId
     *
     * @return NodeInterface
     */
    public function createNodeInstance(
        NodeContext $parentNode,
        NodeContext $afterNode = null,
        NodeContext $sourceNode,
        $userId
    );

    /**
     * Reorder node
     *
     * @param NodeContext $node
     * @param NodeContext $beforeNode
     *
     * @throws InvalidNodeMoveException
     */
    public function reorder(NodeContext $node, NodeContext $beforeNode);

    /**
     * Reorder node
     *
     * @param NodeContext $node
     * @param array       $sortIds
     *
     * @throws InvalidNodeMoveException
     */
    public function reorderChildren(NodeContext $node, array $sortIds);

    /**
     * Move node
     *
     * @param NodeContext $node
     * @param NodeContext $toNode
     * @param string      $uid
     */
    public function move(NodeContext $node, NodeContext $toNode, $uid);

    /**
     * Delete node
     *
     * @param NodeContext $node
     * @param string      $userId
     * @param string      $comment
     */
    public function delete(NodeContext $node, $userId, $comment = null);







    public function isInstance(NodeContext $node);
    public function isInstanceMaster(NodeContext $node);
    public function getPublishedLanguages(NodeContext $node);
    public function isAsync(NodeContext $node);
}
