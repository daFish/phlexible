<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\UserBundle\Model;

use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Model\UserManagerInterface as BaseUserManagerInterface;
use Webmozart\Expression\Expression;

/**
 * User manager interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface UserManagerInterface extends BaseUserManagerInterface
{
    /**
     * Find user
     *
     * @param int $userId
     *
     * @return UserInterface
     */
    public function find($userId);

    /**
     * Find all users
     *
     * @return UserInterface[]
     */
    public function findAll();

    /**
     * @return int
     */
    public function countAll();

    /**
     * Find user by username
     *
     * @param string $username
     *
     * @return UserInterface
     */
    public function findByUsername($username);

    /**
     * @param array      $criteria
     * @param array|null $orderBy
     * @param int|null   $limit
     * @param int|null   $offset
     *
     * @return UserInterface[]
     */
    public function findBy(array $criteria, $orderBy = null, $limit = null, $offset = null);

    /**
     * @param array $criteria
     *
     * @return int
     */
    public function countBy(array $criteria);

    /**
     * @param array $criteria
     * @param array $order
     *
     * @return UserInterface
     */
    public function findOneBy(array $criteria, $order = array());

    /**
     * @return Expression
     */
    public function expr();

    /**
     * @param Expression $expression
     * @param array      $sort
     * @param int|null   $limit
     * @param int|null   $offset
     *
     * @return UserInterface[]
     */
    public function findByExpression(Expression $expression, array $sort = array(), $limit = null, $offset = null);

    /**
     * @param Expression $expression
     *
     * @return int
     */
    public function countByExpression(Expression $expression);

    /**
     * @param Expression $expression
     * @param array|null $sort
     *
     * @return UserInterface
     */
    public function findOneByExpression(Expression $expression, array $sort = null);

    /**
     * @return string
     */
    public function getSystemUserId();

    /**
     * @return string
     */
    public function getSystemUserName();

    /**
     * @return UserInterface
     */
    public function getSystemUser();

    /**
     * @return UserInterface[]
     */
    public function findLoggedInUsers();
}
