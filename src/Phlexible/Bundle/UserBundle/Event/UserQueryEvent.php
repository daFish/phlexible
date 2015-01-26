<?php
/**
 * phlexible
 *
 * @copyright 2007 brainbits GmbH (http://www.brainbits.net)
 * @license   http://www.makeweb.de/LICENCE     Dummy Licence
 */

namespace Phlexible\Bundle\UserBundle\Event;

use Phlexible\Bundle\UserBundle\Model\UserQueryInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * User query event
 *
 * @author  Stephan Wentz <sw@brainbits.net>
 */
class UserQueryEvent extends Event
{
    /**
     * @var UserQueryInterface
     */
    private $userQuery;

    /**
     * Constructor.
     *
     * @param UserQueryInterface $userQuery
     */
    public function __construct(UserQueryInterface $userQuery)
    {
        $this->userQuery = $userQuery;
    }

    /**
     * Return user query
     *
     * @return UserQueryInterface
     */
    public function getUserQuery()
    {
        return $this->userQuery;
    }
}
