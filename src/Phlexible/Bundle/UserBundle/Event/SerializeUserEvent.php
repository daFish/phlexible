<?php
/**
 * phlexible
 *
 * @copyright 2007 brainbits GmbH (http://www.brainbits.net)
 * @license   http://www.makeweb.de/LICENCE     Dummy Licence
 */

namespace Phlexible\Bundle\UserBundle\Event;

use Doctrine\Common\Collections\ArrayCollection;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Serialize user event
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class SerializeUserEvent extends Event
{
    /**
     * @var UserInterface
     */
    private $user;

    /**
     * @var ArrayCollection
     */
    private $userData;

    /**
     * Constructor
     *
     * @param UserInterface   $user
     * @param ArrayCollection $userData
     */
    public function __construct(UserInterface $user, ArrayCollection $userData)
    {
        $this->user = $user;
        $this->userData = $userData;
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
     * Return user data
     *
     * @return ArrayCollection
     */
    public function getUserData()
    {
        return $this->userData;
    }
}
