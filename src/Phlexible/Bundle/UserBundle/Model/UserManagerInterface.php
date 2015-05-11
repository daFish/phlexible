<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\UserBundle\Model;

use Doctrine\Common\Collections\Criteria;
use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Model\UserManagerInterface as BaseUserManagerInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

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
    public function findOneBy(array $criteria, $order = []);

    /**
     * @return Criteria
     */
    public function createCriteria();

    /**
     * @param Criteria   $criteria
     * @param array|null $sort
     * @param int|null   $limit
     * @param int|null   $offset
     *
     * @return \Countable|\Iterator
     */
    public function query(Criteria $criteria, array $sort = null, $limit = null, $offset = null);

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
