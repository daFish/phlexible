<?php
/**
 * phlexible
 *
 * @copyright 2007 brainbits GmbH (http://www.brainbits.net)
 * @license   http://www.makeweb.de/LICENCE     Dummy Licence
 */

namespace Phlexible\Bundle\MessageBundle\Doctrine\Query;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Phlexible\Bundle\MessageBundle\Model\MessageQueryInterface;

/**
 * Message query
 *
 * @author  Stephan Wentz <sw@brainbits.net>
 */
class MessageQuery implements MessageQueryInterface
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
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
            ->select('m')
            ->from('PhlexibleMessageBundle:Message', 'm')
            ->setFirstResult($criteria->getFirstResult())
            ->setMaxResults($criteria->getMaxResults());

        foreach ($criteria->getOrderings() as $field => $dir) {
            $queryBuilder->addOrderBy("m.$field", $dir);
        }

        $this->applyCriteria($queryBuilder, $criteria);

        //echo $queryBuilder->getQuery()->getSQL();die;
        return new Paginator($queryBuilder);
    }

    /**
     * {@inheritdoc}
     */
    public function getFacets(Criteria $criteria)
    {
        $queryBuilder = $this->entityManager->createQueryBuilder()
            ->select('m')
            ->from('PhlexibleMessageBundle:Message', 'm');

        $this->applyCriteria($queryBuilder, $criteria);

        $channelsQb = clone $queryBuilder;
        $channels = $channelsQb->select('DISTINCT m.channel')->getQuery()->getScalarResult();

        $typeQb = clone $queryBuilder;
        $types = $typeQb->select('DISTINCT m.type')->getQuery()->getScalarResult();

        $roleQb = clone $queryBuilder;
        $roles = $roleQb->select('DISTINCT m.role')->getQuery()->getScalarResult();

        $toInt = function($v) {
            return (int) $v;
        };

        return [
            'types'      => array_map($toInt, array_column($types, 'type')),
            'channels'   => array_column($channels, 'channel'),
            'roles'      => array_column($roles, 'role'),
        ];
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
