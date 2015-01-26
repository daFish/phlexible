<?php
/**
 * phlexible
 *
 * @copyright 2007 brainbits GmbH (http://www.brainbits.net)
 * @license   http://www.makeweb.de/LICENCE     Dummy Licence
 */

namespace Phlexible\Bundle\UserBundle\Event;

use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * User apply values event
 *
 * @author  Stephan Wentz <sw@brainbits.net>
 */
class UserApplyValuesEvent extends Event
{
    /**
     * @var UserInterface
     */
    protected $user = null;

    /**
     * @var array
     */
    protected $values = null;

    /**
     * Constructor
     *
     * @param UserInterface $user
     * @param array         $values
     */
    public function __construct(UserInterface $user, array $values)
    {
        $this->user = $user;
        $this->values = $values;
    }

    /**
     * Return user
     *
     * @return UserInterface
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Return values
     *
     * @return array
     */
    public function getValues()
    {
        return $this->values;
    }
}
