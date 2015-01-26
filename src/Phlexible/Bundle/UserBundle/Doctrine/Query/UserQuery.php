<?php
/**
 * phlexible
 *
 * @copyright 2007 brainbits GmbH (http://www.brainbits.net)
 * @license   http://www.makeweb.de/LICENCE     Dummy Licence
 */

namespace Phlexible\Bundle\UserBundle\Doctrine\Query;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\Expr;
use Phlexible\Bundle\UserBundle\Event\UserQueryEvent;
use Phlexible\Bundle\UserBundle\Model\UserQueryInterface;
use Phlexible\Bundle\UserBundle\UserEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * User query
 *
 * @author  Stephan Wentz <sw@brainbits.net>
 */
class UserQuery implements \Countable, UserQueryInterface
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var QueryBuilder
     */
    private $queryBuilder;

    /**
     * @param EntityManager            $entityManager
     * @param EventDispatcherInterface $eventDispatcher
     * @param string                   $userClassname
     */
    public function __construct(EntityManager $entityManager, EventDispatcherInterface $eventDispatcher, $userClassname)
    {
        $this->entityManager = $entityManager;
        $this->eventDispatcher = $eventDispatcher;

        $this->queryBuilder = $this->entityManager->createQueryBuilder()
            ->add('select', new Expr\Select('u'))
            ->add('from', new Expr\From($userClassname, 'u'));

        $this->where = $this->queryBuilder->expr()->andX();
    }

    /**
     * {@inheritdoc}
     */
    public function limit($start = 0, $limit = 20)
    {
        $this->queryBuilder
            ->setFirstResult($start)
            ->setMaxResults($limit);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function sort($field, $dir = 'asc')
    {
        $this->queryBuilder
            ->add('orderBy', new Expr\OrderBy('u.' . $field, $dir));

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function byValue($value)
    {
        $orExpr = $this->queryBuilder->expr()->orX();
        $orExpr->add($this->queryBuilder->expr()->like('u.username', ':value'));
        $orExpr->add($this->queryBuilder->expr()->like('u.email', ':value'));
        $orExpr->add($this->queryBuilder->expr()->like('u.firstname', ':value'));
        $orExpr->add($this->queryBuilder->expr()->like('u.lastname', ':value'));
        $orExpr->add($this->queryBuilder->expr()->like('u.comment', ':value'));
        $this->where->add($orExpr);
        $this->queryBuilder->setParameter('value', '%' . $value . '%');

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function byAccountDisabled($disabled = true)
    {
        $expr = $this->queryBuilder->expr()->eq('u.enabled', $disabled ? '0' : '1');
        $this->where->add($expr);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function byAccountExpired($expired = true)
    {
        $now = new \DateTime();
        $now = $now->format('Y-m-d H:i:s');

        $orExpr = new Expr\Orx();
        $orExpr->add($this->queryBuilder->expr()->eq('u.expired', $expired ? '1' : '0'));
        $orExpr->add($this->queryBuilder->expr()->lte('u.expiresAt', ':now'));

        $this->where->add($orExpr);
        $this->queryBuilder->setParameter('now', $now);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function byAccountHasExpireDate($hasExpireDate = true)
    {
        if ($hasExpireDate) {
            $expr = $this->queryBuilder->expr()->isNotNull('u.expiresAt');
        } else {
            $expr = $this->queryBuilder->expr()->isNull('u.expiresAt');
        }

        $this->where->add($expr);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function byRole($role)
    {
        $expr = $this->queryBuilder->expr()->like('u.roles', ':' . strtolower(str_replace('_', '', $role)));
        $this->where->add($expr);
        $this->queryBuilder->setParameter(strtolower(str_replace('_', '', $role)), '%' . substr($role, 5) . '%');

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function byGroup($group)
    {
        $expr = $this->queryBuilder->expr()->like('u.groups', ':' . strtolower(str_replace('_', '', $group)));
        $this->where->add($expr);
        $this->queryBuilder->setParameter(strtolower(str_replace('_', '', $group)), '%' . substr($group, 5) . '%');

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        $countQueryBuilder = clone $this->queryBuilder;

        $countQueryBuilder
            ->setFirstResult(0)
            ->setMaxResults(1)
            ->resetDQLPart('select')
            ->add('select', $countQueryBuilder->expr()->count('u.id'));

        if ($this->where->count()) {
            $countQueryBuilder->add('where', $this->where);
        }

        $countQuery = $countQueryBuilder->getQuery();
        $count      = $countQuery->getSingleScalarResult($countQuery);

        return $count;
    }

    /**
     * {@inheritdoc}
     */
    public function getResult()
    {
        $this->eventDispatcher->dispatch(
            UserEvents::USER_QUERY,
            new UserQueryEvent($this)
        );

        $usersQueryBuilder = clone $this->queryBuilder;

        if ($this->where->count()) {
            $usersQueryBuilder->add('where', $this->where);
        }

        $usersQuery = $usersQueryBuilder->getQuery();
        $foundUsers = $usersQuery->getResult();

        return $foundUsers;
    }

    /**
     * {@inheritdoc}
     */
    public function getQueryBuilder()
    {
        return $this->queryBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function getWhere()
    {
        return $this->where;
    }
}
