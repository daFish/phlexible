<?php
/**
 * phlexible
 *
 * @copyright 2007 brainbits GmbH (http://www.brainbits.net)
 * @license   http://www.makeweb.de/LICENCE     Dummy Licence
 */

namespace Phlexible\Bundle\UserBundle\Doctrine\Query;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Phlexible\Bundle\UserBundle\Event;
use Phlexible\Bundle\UserBundle\Event\UserQueryApplyCriteriaEvent;
use Phlexible\Bundle\UserBundle\Model\UserQueryInterface;
use Phlexible\Bundle\UserBundle\UserEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * User query
 *
 * @author  Stephan Wentz <sw@brainbits.net>
 */
class UserQuery implements UserQueryInterface
{
    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var string
     */
    protected $userClassname;

    /**
     * @param EntityManager            $entityManager
     * @param EventDispatcherInterface $eventDispatcher
     * @param string                   $userClassname
     */
    public function __construct(EntityManager $entityManager, EventDispatcherInterface $eventDispatcher, $userClassname)
    {
        $this->entityManager = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->userClassname = $userClassname;
    }

    /**
     * Return query result
     *
     * @param Criteria $criteria
     *
     * @return Paginator
     */
    public function getResult(Criteria $criteria)
    {
        $queryBuilder = $this->entityManager->createQueryBuilder()
            ->select('u')
            ->from($this->userClassname, 'u')
            ->setFirstResult($criteria->getFirstResult())
            ->setMaxResults($criteria->getMaxResults());

        foreach ($criteria->getOrderings() as $field => $dir) {
            $queryBuilder->addOrderBy("u.$field", $dir);
        }

        $this->eventDispatcher->dispatch(UserEvents::USER_QUERY_APPLY_CRITERIA, new UserQueryApplyCriteriaEvent($criteria));

        $this->applyCriteria($queryBuilder, $criteria);

        return new Paginator($queryBuilder);
    }

    /**
     * Apply filter
     *
     * @param QueryBuilder $queryBuilder
     * @param Criteria     $criteria
     */
    private function applyCriteria(QueryBuilder $queryBuilder, Criteria $criteria)
    {
        $expr = $criteria->getWhereExpression();

        if ($expr) {
            $visitor  = new ExpressionVisitor($queryBuilder);
            $where = $visitor->dispatch($expr);
            $queryBuilder->where($where);
        }
    }
}
