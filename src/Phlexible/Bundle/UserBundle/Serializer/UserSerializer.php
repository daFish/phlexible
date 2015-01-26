<?php
/**
 * phlexible
 *
 * @copyright 2007 brainbits GmbH (http://www.brainbits.net)
 * @license   http://www.makeweb.de/LICENCE     Dummy Licence
 */

namespace Phlexible\Bundle\UserBundle\Serializer;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\Expr;
use FOS\UserBundle\Model\UserInterface;
use Phlexible\Bundle\UserBundle\Event\SerializeUserEvent;
use Phlexible\Bundle\UserBundle\Model\UserQueryInterface;
use Phlexible\Bundle\UserBundle\UserEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * User serializer
 *
 * @author  Stephan Wentz <sw@brainbits.net>
 */
class UserSerializer
{
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param UserInterface $user
     *
     * @return array
     */
    public function serialize(UserInterface $user)
    {
        $userData = new \ArrayObject(
            array(
                'id'         => $user->getId(),
                'username'   => $user->getUsername(),
                'email'      => $user->getEmail(),
                'emailHash'  => md5(strtolower($user->getEmail())),
                'firstname'  => $user->getFirstname(),
                'lastname'   => $user->getLastname(),
                'comment'    => $user->getComment(),
                'expired'    => $user->isExpired() ? 1 : 0,
                'expiresAt'  => $user->getExpiresAt() ? $user->getExpiresAt()->format('Y-m-d') : '',
                'disabled'   => $user->isEnabled() ? 0 : 1,
                'createDate' => $user->getCreatedAt()
                    ? $user->getCreatedAt()->format('Y-m-d H:i:s')
                    : '',
                'createUser' => '',//$user->getCreateUserId(),
                'modifyDate' => $user->getModifiedAt()
                    ? $user->getModifiedAt()->format('Y-m-d H:i:s')
                    : '',
                'modifyUser' => '',//$foundUser->getModifyUserId(),
                'properties' => $user->getProperties(),
                'roles'      => $user->getRoles(),
                'extra'      => new \ArrayObject(),
            )
        );

        $this->eventDispatcher->dispatch(
            UserEvents::SERIALIZE_USER,
            new SerializeUserEvent($user, $userData)
        );

        return (array) $userData;
    }
}
