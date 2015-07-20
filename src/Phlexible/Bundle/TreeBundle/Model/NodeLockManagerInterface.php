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
     * Is element locked by either master or slave?
     *
     * @param NodeContext $node
     * @param string      $language
     *
     * @return bool
     */
    public function isLocked(NodeContext $node, $language);

    /**
     * @param NodeContext $node
     *
     * @return bool
     */
    public function isMasterLocked(NodeContext $node);

    /**
     * @param NodeContext $node
     * @param string      $language
     *
     * @return bool
     */
    public function isSlaveLocked(NodeContext $node, $language);

    /**
     * @param NodeContext $node
     * @param string      $language
     * @param string      $userId
     *
     * @return bool
     */
    public function isLockedByUser(NodeContext $node, $language, $userId);

    /**
     * @param NodeContext $node
     * @param string      $userId
     *
     * @return bool
     */
    public function isMasterLockedByUser(NodeContext $node, $userId);

    /**
     * @param NodeContext $node
     * @param string      $language
     * @param string      $userId
     *
     * @return bool
     */
    public function isSlaveLockedByUser(NodeContext $node, $language, $userId);

    /**
     * @param NodeContext $node
     * @param string      $language
     * @param string      $userId
     *
     * @return bool
     */
    public function isLockedByOtherUser(NodeContext $node, $language, $userId);

    /**
     * @param NodeContext $node
     * @param string      $userId
     *
     * @return bool
     */
    public function isMasterLockedByOtherUser(NodeContext $node, $userId);

    /**
     * @param NodeContext $node
     * @param string      $language
     * @param string      $userId
     *
     * @return bool
     */
    public function isSlaveLockedByOtherUser(NodeContext $node, $language, $userId);

    /**
     * @param NodeContext $node
     * @param string      $userId
     * @param string      $language
     * @param string      $type
     *
     * @return NodeLock
     */
    public function lock(NodeContext $node, $userId, $language = null, $type = NodeLock::TYPE_TEMPORARY);

    /**
     * @param NodeContext $node
     * @param string      $language
     */
    public function unlock(NodeContext $node, $language = null);

    /**
     * @param string $id
     *
     * @return NodeLock
     */
    public function find($id);

    /**
     * @param NodeContext $node
     *
     * @return NodeLock|null
     */
    public function findMasterLock(NodeContext $node);

    /**
     * @param NodeContext $node
     * @param string      $language
     *
     * @return NodeLock|null
     */
    public function findSlaveLock(NodeContext $node, $language);

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
