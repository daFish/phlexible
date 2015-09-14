<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\Node\Model;

/**
 * Node manager interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface NodeManagerInterface
{
    /**
     * @param int $id
     *
     * @return NodeInterface
     */
    public function find($id);

    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return NodeInterface
     */
    public function findOneBy(array $criteria, array $orderBy = null);

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param int   $limit
     * @param int   $offset
     *
     * @return NodeInterface[]
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null);

    /**
     * @param array $instanceTypes
     * @param array $criteria
     * @param array $orderBy
     *
     * @return NodeInterface
     */
    public function findOneByNodeType(array $instanceTypes = array(), array $criteria, array $orderBy = null);

    /**
     * @param array $instanceTypes
     * @param array $criteria
     * @param array $orderBy
     * @param int   $limit
     * @param int   $offset
     *
     * @return NodeInterface[]
     */
    public function findByNodeType(array $instanceTypes = array(), array $criteria, array $orderBy = null, $limit = null, $offset = null);

    /**
     * @param NodeInterface $node
     *
     * @return bool
     */
    public function isInstance(NodeInterface $node);

    /**
     * @param NodeInterface $node
     *
     * @return bool
     */
    public function isInstanceMaster(NodeInterface $node);

    /**
     * Return instance nodes for the given nodes from THIS tree.
     *
     * @param NodeInterface $node
     *
     * @return NodeInterface[]
     */
    public function getInstanceNodes(NodeInterface $node);

    /**
     * @param NodeInterface $node
     * @param bool          $flush
     *
     * @return $this
     */
    public function updateNode(NodeInterface $node, $flush = true);

    /**
     * @param NodeInterface $node
     *
     * @return $this
     */
    public function deleteNode(NodeInterface $node);
}
