<?php
/**
 * phlexible
 *
 * @copyright 2007 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\UserBundle;

/**
 * User events
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class UserEvents
{
    /**
     * Fired before a new user has been created
     */
    const BEFORE_CREATE_USER = 'phlexible_user.before_create_user';

    /**
     * Fired after a new user has been created
     */
    const CREATE_USER = 'phlexible_user.create_user';

    /**
     * Fired after a user has been updated
     */
    const BEFORE_UPDATE_USER = 'phlexible_user.before_update_user';

    /**
     * Fired after a user has been updated
     */
    const UPDATE_USER = 'phlexible_user.update_user';

    /**
     * Fired before a user has been deleted
     */
    const BEFORE_DELETE_USER = 'phlexible_user.before_delete_user';

    /**
     * Fired after a user has been deleted
     */
    const DELETE_USER = 'phlexible_user.delete_user';

    /**
     * Fired before a new group has been created
     */
    const BEFORE_CREATE_GROUP = 'phlexible_user.before_create_group';

    /**
     * Fired after a new group has been created
     */
    const CREATE_GROUP = 'phlexible_user.after_create_group';

    /**
     * Fired after a group has been updated
     */
    const BEFORE_UPDATE_GROUP = 'phlexible_user.before_update_group';

    /**
     * Fired after a group has been updated
     */
    const UPDATE_GROUP = 'phlexible_user.update_group';

    /**
     * Fired before a group has been deleted
     */
    const BEFORE_DELETE_GROUP = 'phlexible_user.before_delete_group';

    /**
     * Fired after a group has been deleted
     */
    const DELETE_GROUP = 'phlexible_user.after_delete_group';

    /**
     * Fired on user query
     */
    const USER_QUERY_APPLY_CRITERIA = 'phlexible_user.user_query_apply_criteria';

    /**
     * Fired on serialize user
     */
    const SERIALIZE_USER = 'phlexible_user.serialize_user';
}
