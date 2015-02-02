<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MessageBundle\Model;

use Doctrine\Common\Collections\Criteria;
use Phlexible\Bundle\MessageBundle\Entity\Message;
use Phlexible\Bundle\MessageBundle\Exception\LogicException;

/**
 * Message manager
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface MessageManagerInterface
{
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
     * @return Message[]
     */
    public function findOneBy(array $criteria, $orderBy = null);

    /**
     * @return Criteria
     */
    public function createCriteria();

    /**
     * @param Criteria $criteria
     *
     * @return \Countable|\Iterator
     */
    public function query(Criteria $criteria);

    /**
     * Get priority map
     *
     * @return array
     */
    public function getPriorityNames();

    /**
     * Return type map
     *
     * @return array
     */
    public function getTypeNames();

    /**
     * Return facets
     *
     * @param Criteria $criteria
     *
     * @return array
     */
    public function getFacets(Criteria $criteria);

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
