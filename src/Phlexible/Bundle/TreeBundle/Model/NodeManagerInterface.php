<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\Model;

use Phlexible\Bundle\TreeBundle\Entity\NodeState;

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
     * @return NodeInterface
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null);

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
     * @param array $criteria
     *
     * @return NodeState[]
     */
    public function findStateBy(array $criteria);

    /**
     * @param array $criteria
     *
     * @return NodeState
     */
    public function findOneStateBy(array $criteria);

    /**
     * @param NodeInterface $node
     * @param int               $version
     * @param string            $language
     *
     * @return string
     */
    public function hashNode(NodeInterface $node, $version, $language);

    /**
     * @param NodeInterface $node
     * @param bool              $flush
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

    /**
     * @param NodeState $nodeOnline
     */
    public function updateState(NodeState $nodeOnline);

    /**
     * @param NodeState $nodeOnline
     */
    public function deleteState(NodeState $nodeOnline);
}
