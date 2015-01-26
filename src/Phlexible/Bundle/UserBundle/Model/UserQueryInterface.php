<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\UserBundle\Model;

use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\Expr;

/**
 * User query
 *
 * @author  Stephan Wentz <sw@brainbits.net>
 */
interface UserQueryInterface
{
    /**
     * Limit query
     *
     * @param int $start
     * @param int $limit
     *
     * @return $this
     */
    public function limit($start = 0, $limit = 20);

    /**
     * Sort query
     *
     * @param string $field
     * @param string $dir
     *
     * @return $this
     */
    public function sort($field, $dir = 'asc');

    /**
     * Filter by value
     *
     * @param string $value
     *
     * @return $this
     */
    public function byValue($value);

    /**
     * Filter by account disabled
     *
     * @param bool $disabled
     *
     * @return $this
     */
    public function byAccountDisabled($disabled = true);

    /**
     * Filter by account expired
     *
     * @param bool $expired
     *
     * @return $this
     */
    public function byAccountExpired($expired = true);

    /**
     * Filter by account has expire date
     *
     * @param bool $hasExpireDate
     *
     * @return $this
     */
    public function byAccountHasExpireDate($hasExpireDate = true);

    /**
     * Filter by role
     *
     * @param string $role
     *
     * @return $this
     */
    public function byRole($role);

    /**
     * Filter by group
     *
     * @param string $group
     *
     * @return $this
     */
    public function byGroup($group);

    /**
     * Count result
     *
     * @return int
     */
    public function count();

    /**
     * Return users
     *
     * @return array
     */
    public function getResult();

    /**
     * Return query builder
     *
     * @return QueryBuilder
     */
    public function getQueryBuilder();

    /**
     * Return where
     *
     * @return Expr\Andx
     */
    public function getWhere();
}