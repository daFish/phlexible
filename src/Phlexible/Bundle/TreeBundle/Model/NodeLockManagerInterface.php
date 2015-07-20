<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\Model;

use Phlexible\Bundle\TreeBundle\Entity\NodeLock;
use Phlexible\Bundle\TreeBundle\Node\NodeContext;

/**
 * Node lock manager interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @author Phillip Look <pl@brainbits.net>
 */
interface NodeLockManagerInterface
{
    /**
     * @param NodeContext $node
     *
     * @return bool
     */
    public function isLocked(NodeContext $node);

    /**
     * @param NodeContext $node
     * @param string      $userId
     *
     * @return bool
     */
    public function isLockedByUser(NodeContext $node, $userId);

    /**
     * @param NodeContext $node
     * @param string      $userId
     *
     * @return bool
     */
    public function isLockedByOtherUser(NodeContext $node, $userId);

    /**
     * @param NodeContext $node
     * @param string      $userId
     * @param string      $type
     *
     * @return NodeLock
     */
    public function lock(NodeContext $node, $userId, $type = NodeLock::TYPE_TEMPORARY);

    /**
     * @param NodeContext $node
     */
    public function unlock(NodeContext $node);

    /**
     * @param NodeContext $node
     *
     * @return NodeLock
     */
    public function findLock(NodeContext $node);

    /**
     * @param string $id
     *
     * @return NodeLock
     */
    public function find($id);

    /**
     * @return NodeLock[]
     */
    public function findAll();

    /**
     * @param array    $criteria
     * @param array    $sort
     * @param int|null $limit
     * @param int|null $offset
     *
     * @return NodeLock[]
     */
    public function findBy(array $criteria, array $sort = array(), $limit = null, $offset = null);

    /**
     * @param NodeLock $lock
     */
    public function deleteLock(NodeLock $lock);
}
