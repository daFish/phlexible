<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\TreeBundle;

/**
 * Tree events.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class TreeEvents
{
    /**
     * Fired after configuring navigation.
     */
    const CONFIGURE_NAVIGATION = 'phlexible_tree.configure_navigation';

    /**
     * Fired after configuring node.
     */
    const CONFIGURE_TREE_NODE = 'phlexible_tree.configure_tree_node';

    /**
     * Fired before a new node is created.
     */
    const BEFORE_CREATE_NODE = 'phlexible_tree.before_create_node';

    /**
     * Fired after a new node has been created.
     */
    const CREATE_NODE = 'phlexible_tree.create_node';

    /**
     * Fired before a node instance is created.
     */
    const BEFORE_CREATE_NODE_INSTANCE = 'phlexible_tree.before_create_node_instance';

    /**
     * Fired after a node instance has been created.
     */
    const CREATE_NODE_INSTANCE = 'phlexible_tree.create_node_instance';

    /**
     * Fired before a node is updated.
     */
    const BEFORE_UPDATE_NODE = 'phlexible_tree.before_update_node';

    /**
     * Fired after a node has updated.
     */
    const UPDATE_NODE = 'phlexible_tree.update_node';

    /**
     * Fired before a node is deleted.
     */
    const BEFORE_DELETE_NODE = 'phlexible_tree.before_delete_node';

    /**
     * Fired after a node is deleted.
     */
    const DELETE_NODE = 'phlexible_tree.delete_node';

    /**
     * Fired before a node is published.
     */
    const BEFORE_CREATE_STATE = 'phlexible_tree.before_create_state';

    /**
     * Fired after a node is published.
     */
    const CREATE_STATE = 'phlexible_tree.create_state';

    /**
     * Fired before a node is published.
     */
    const BEFORE_UPDATE_STATE = 'phlexible_tree.before_update_state';

    /**
     * Fired after a node is published.
     */
    const UPDATE_STATE = 'phlexible_tree.update_state';

    /**
     * Fired before a node is set offline.
     */
    const BEFORE_DELETE_STATE = 'phlexible_tree.before_delete_state';

    /**
     * Fired after a node is set offline.
     */
    const DELETE_STATE = 'phlexible_tree.delete_state';

    /**
     * Fired before a node is published.
     */
    const BEFORE_CREATE_NODE_CONTEXT = 'phlexible_tree.before_create_node_context';

    /**
     * Fired after a node is published.
     */
    const CREATE_NODE_CONTEXT = 'phlexible_tree.create_node_context';

    /**
     * Fired after a node is published.
     */
    const BEFORE_CREATE_NODE_INSTANCE_CONTEXT = 'phlexible_tree.before_create_node_instance_context';

    /**
     * Fired after a node is published.
     */
    const CREATE_NODE_INSTANCE_CONTEXT = 'phlexible_tree.create_node_instance_context';

    /**
     * Fired before a node is published.
     */
    const BEFORE_UPDATE_NODE_CONTEXT = 'phlexible_tree.before_update_node_context';

    /**
     * Fired after a node is published.
     */
    const UPDATE_NODE_CONTEXT = 'phlexible_tree.update_node_context';

    /**
     * Fired before a node is published.
     */
    const BEFORE_DELETE_NODE_CONTEXT = 'phlexible_tree.before_delete_node_context';

    /**
     * Fired after a node is published.
     */
    const DELETE_NODE_CONTEXT = 'phlexible_tree.delete_node_context';

    /**
     * Fired before a node is published.
     */
    const BEFORE_PUBLISH_NODE_CONTEXT = 'phlexible_tree.before_publish_node_context';

    /**
     * Fired after a node is published.
     */
    const PUBLISH_NODE_CONTEXT = 'phlexible_tree.publish_node_context';

    /**
     * Fired before a node is set offline.
     */
    const BEFORE_SET_NODE_OFFLINE_CONTEXT = 'phlexible_tree.before_set_node_offline_context';

    /**
     * Fired after a node is set offline.
     */
    const SET_NODE_OFFLINE_CONTEXT = 'phlexible_tree.set_node_offline_context';

    /**
     * Fired before a node is moved.
     */
    const BEFORE_MOVE_NODE_CONTEXT = 'phlexible_tree.before_move_node_context';

    /**
     * Fired after a node has been moved.
     */
    const MOVE_NODE_CONTEXT = 'phlexible_tree.move_node_context';

    /**
     * Fired before node is reordered.
     */
    const BEFORE_REORDER_NODE_CONTEXT = 'phlexible_tree.before_reorder_node_context';

    /**
     * Fired after nodes have been reordered.
     */
    const REORDER_NODE_CONTEXT = 'phlexible_tree.reorder_node_context';

    /**
     * Fired before node children are reordered.
     */
    const BEFORE_REORDER_CHILD_NODES_CONTEXT = 'phlexible_tree.before_reorder_child_nodes_context';

    /**
     * Fired after node children have been reordered.
     */
    const REORDER_CHILD_NODES_CONTEXT = 'phlexible_tree.reorder_child_nodes_context';

    /**
     * Fired when the is filtered.
     */
    const TREE_FILTER = 'phlexible_tree.tree_filter';

    /**
     * Fired before a new route is created.
     */
    const BEFORE_CREATE_ROUTE = 'phlexible_tree.before_create_route';

    /**
     * Fired after a new route has been created.
     */
    const CREATE_ROUTE = 'phlexible_tree.create_route';

    /**
     * Fired before a route is updated.
     */
    const BEFORE_UPDATE_ROUTE = 'phlexible_tree.before_update_route';

    /**
     * Fired after a route has updated.
     */
    const UPDATE_ROUTE = 'phlexible_tree.update_route';

    /**
     * Fired before a route is deleted.
     */
    const BEFORE_DELETE_ROUTE = 'phlexible_tree.before_delete_route';

    /**
     * Fired after a route is deleted.
     */
    const DELETE_ROUTE = 'phlexible_tree.delete_route';
}
