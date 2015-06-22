<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\UserBundle\Doctrine;

use Doctrine\Common\Persistence\ObjectManager;
use FOS\UserBundle\Doctrine\UserManager as BaseUserManager;
use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Util\CanonicalizerInterface;
use Phlexible\Bundle\UserBundle\Entity\Repository\UserRepository;
use Phlexible\Bundle\UserBundle\Event\UserEvent;
use Phlexible\Bundle\UserBundle\Model\UserManagerInterface;
use Phlexible\Bundle\UserBundle\UserEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Webmozart\Expression\Expr;
use Webmozart\Expression\Expression;

/**
 * User manager
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class UserManager extends BaseUserManager implements UserManagerInterface
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var string
     */
    private $systemUserId;

    /**
     * @var string
     */
    private $everyoneGroupId;

    /**
     * @param EncoderFactoryInterface  $encoderFactory
     * @param CanonicalizerInterface   $usernameCanonicalizer
     * @param CanonicalizerInterface   $emailCanonicalizer
     * @param ObjectManager            $om
     * @param string                   $class
     * @param EventDispatcherInterface $dispatcher
     * @param string                   $systemUserId
     * @param string                   $everyoneGroupId
     */
    public function __construct(
        EncoderFactoryInterface $encoderFactory,
        CanonicalizerInterface $usernameCanonicalizer,
        CanonicalizerInterface $emailCanonicalizer,
        ObjectManager $om,
        $class,
        EventDispatcherInterface $dispatcher,
        $systemUserId,
        $everyoneGroupId)
    {
        parent::__construct($encoderFactory, $usernameCanonicalizer, $emailCanonicalizer, $om, $class);

        $this->dispatcher = $dispatcher;
        $this->systemUserId = $systemUserId;
        $this->everyoneGroupId = $everyoneGroupId;
    }

    /**
     * @return UserRepository
     */
    private function getUserRepository()
    {
        if ($this->userRepository === null) {
            $this->userRepository = $this->objectManager->getRepository($this->getClass());
        }

        return $this->userRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function find($userId)
    {
        return $this->getUserRepository()->find($userId);
    }

    /**
     * {@inheritdoc}
     */
    public function findAll()
    {
        return $this->getUserRepository()->findAll();
    }

    /**
     * {@inheritdoc}
     */
    public function countAll()
    {
        $qb = $this->getUserRepository()->createQueryBuilder('u');
        $qb
            ->select('COUNT(u.id)');

        return $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * {@inheritdoc}
     */
    public function findByUsername($username)
    {
        return $this->findOneBy(['username' => $username]);
    }

    /**
     * {@inheritdoc}
     */
    public function findBy(array $criteria, $orderBy = null, $limit = null, $offset = null)
    {
        return $this->getUserRepository()->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * {@inheritdoc}
     */
    public function countBy(array $criteria)
    {
        $qb = $this->getUserRepository()->createQueryBuilder('u');
        $qb
            ->select('COUNT(u.id)');

        foreach ($criteria as $key => $value) {
            $qb->andWhere($qb->expr()->eq($key, $qb->expr()->literal($value)));
        }

        return $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * {@inheritdoc}
     */
    public function findOneBy(array $criteria, $order = [])
    {
        return $this->getUserRepository()->findOneBy($criteria, $order);
    }

    /**
     * {@inheritdoc}
     */
    public function expr()
    {
        return Expr::true();
    }

    /**
     * {@inheritdoc}
     */
    public function findByExpression(Expression $expr, array $sort = null, $limit = null, $offset = null)
    {
        return $this->getUserRepository()->findByExpression($expr, $sort, $limit, $offset);
    }

    /**
     * {@inheritdoc}
     */
    public function countByExpression(Expression $expr)
    {
        return $this->getUserRepository()->countByExpression($expr);
    }

    /**
     * {@inheritdoc}
     */
    public function findOneByExpression(Expression $expr, array $sort = null)
    {
        return $this->getUserRepository()->findOneByExpression($expr, $sort);
    }

    /**
     * {@inheritdoc}
     */
    public function getSystemUserId()
    {
        return $this->systemUserId;
    }

    /**
     * {@inheritdoc}
     */
    public function getSystemUserName()
    {
        return $this->getSystemUser()->getUsername();
    }

    /**
     * {@inheritdoc}
     */
    public function getSystemUser()
    {
        return $this->find($this->getSystemUserId());
    }

    /**
     * {@inheritdoc}
     */
    public function findLoggedInUsers()
    {
        $qb = $this->getUserRepository()->createQueryBuilder('u');
        $qb->where($qb->expr()->gte('u.modifiedAt', $qb->expr()->literal(date('Y-m-d H:i:s'))));

        return $qb->getQuery()->getResult();
    }

    /**
     * {@inheritdoc}
     */
    public function checkUsername($username)
    {
        return $this->findOneBy(['username' => $username]) ? true : false;
    }

    /**
     * {@inheritdoc}
     */
    public function checkEmail($email)
    {
        return $this->findOneBy(['email' => $email]) ? true : false;
    }

    /**
     * {@inheritdoc}
     */
    public function updateUser(UserInterface $user, $andFlush = true)
    {
        $this->updateCanonicalFields($user);
        $this->updatePassword($user);

        $isUpdate = false;
        if ($this->objectManager->contains($user)) {
            $isUpdate = true;
        }

        $event = new UserEvent($user);
        if ($isUpdate) {
            $this->dispatcher->dispatch(UserEvents::BEFORE_UPDATE_USER, $event);
        } else {
            $this->dispatcher->dispatch(UserEvents::BEFORE_CREATE_USER, $event);
        }
        if ($event->isPropagationStopped()) {
            return;
        }

        $this->objectManager->persist($user);
        if ($andFlush) {
            $this->objectManager->flush();
        }

        $event = new UserEvent($user);
        if ($isUpdate) {
            $this->dispatcher->dispatch(UserEvents::UPDATE_USER, $event);
        } else {
            $this->dispatcher->dispatch(UserEvents::CREATE_USER, $event);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function deleteUser(UserInterface $user)
    {
        $event = new UserEvent($user);
        if ($this->dispatcher->dispatch(UserEvents::BEFORE_DELETE_USER, $event)->isPropagationStopped()) {
            return;
        }

        $this->deleteUser($user);

        $event = new UserEvent($user);
        $this->dispatcher->dispatch(UserEvents::DELETE_USER, $event);
    }
}
