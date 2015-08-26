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

use Phlexible\Bundle\TreeBundle\Entity\NodeChange;
use Phlexible\Bundle\TreeBundle\Node\NodeContext;

/**
 * Node history managerInterface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface NodeChangeManagerInterface
{
    const ACTION_CREATE_ELEMENT         = 'createElement';
    const ACTION_CREATE_ELEMENT_VERSION = 'createElementVersion';
    const ACTION_SAVE_ELEMENT           = 'saveElement';
    const ACTION_SAVE_ELEMENT_MASTER    = 'saveElementMaster';
    const ACTION_SAVE_ELEMENT_SLAVE     = 'saveElementSlave';

    const ACTION_CREATE_NODE          = 'createNode';
    const ACTION_UPDATE_NODE          = 'updateNode';
    const ACTION_DELETE_NODE          = 'deleteNode';
    const ACTION_MOVE_NODE            = 'moveNode';
    const ACTION_CREATE_NODE_INSTANCE = 'createNodeInstance';
    const ACTION_PUBLISH_NODE         = 'publishNode';
    const ACTION_SET_NODE_OFFLINE     = 'setNodeOffline';

    const ACTION_CREATE_TEASER          = 'createTeaser';
    const ACTION_DELETE_TEASER          = 'deleteTeaser';
    const ACTION_CREATE_TEASER_INSTANCE = 'createInstance';
    const ACTION_PUBLISH_TEASER         = 'publishTeaser';
    const ACTION_SET_TEASER_OFFLINE     = 'setTeaserOffline';

    /**
     * @param array      $criteria
     * @param array|null $orderBy
     * @param int|null   $limit
     * @param int|null   $offset
     *
     * @return NodeChange[]
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null);

    /**
     * @param array $criteria
     *
     * @return int
     */
    public function countBy(array $criteria);

    /**
     * Insert new history entry
     *
     * @param string      $action
     * @param NodeContext $node
     * @param string      $userId
     * @param string      $version
     * @param string      $language
     * @param string      $comment
     *
     * @return $this
     */
    public function insert($action, NodeContext $node, $userId, $version = null, $language = null, $comment = null);
}
