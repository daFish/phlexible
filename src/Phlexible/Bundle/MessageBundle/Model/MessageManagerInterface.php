<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\MessageBundle\Model;

use Phlexible\Bundle\MessageBundle\Entity\Message;
use Phlexible\Bundle\MessageBundle\Exception\LogicException;
use Webmozart\Expression\Expression;

/**
 * Message manager
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface MessageManagerInterface
{
    /**
     * Find message
     *
     * @param string $id
     *
     * @return Message
     */
    public function find($id);

    /**
     * Find messages
     *
     * @param array $criteria
     * @param null  $orderBy
     * @param null  $limit
     * @param null  $offset
     *
     * @return Message[]
     */
    public function findBy(array $criteria, $orderBy = null, $limit = null, $offset = null);

    /**
     * Find message
     *
     * @param array $criteria
     * @param null  $orderBy
     *
     * @return Message
     */
    public function findOneBy(array $criteria, $orderBy = null);

    /**
     * @return Expression
     */
    public function expr();

    /**
     * @param Expression $expression
     * @param array      $orderBy
     * @param int        $limit
     * @param int        $offset
     *
     * @return Message[]
     */
    public function findByExpression(Expression $expression, $orderBy = null, $limit = null, $offset = null);

    /**
     * @param Expression $expression
     *
     * @return int
     */
    public function countByExpression(Expression $expression);

    /**
     * @param Expression $expression
     * @param array      $orderBy
     *
     * @return Message
     */
    public function findOneByExpression(Expression $expression, $orderBy = null);

    /**
     * Return type map
     *
     * @return array
     */
    public function getTypeNames();

    /**
     * Return facets
     *
     * @return array
     */
    public function getFacets();

    /**
     * Return facets by expression
     *
     * @param Expression $expression
     *
     * @return array
     */
    public function getFacetsByExpression(Expression $expression);

    /**
     * Update message
     *
     * @param Message $message
     *
     * @throws LogicException
     */
    public function updateMessage(Message $message);

    /**
     * Delete message
     *
     * @param Message $message
     */
    public function deleteMessage(Message $message);
}
