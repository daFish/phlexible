<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\UserBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Phlexible\Bundle\MessageBundle\Entity\Message;
use Phlexible\Component\Expression\Traversal\QueryBuilderExpressionVisitor;
use Webmozart\Expression\Expression;

/**
 * User repository
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class UserRepository extends EntityRepository
{
    /**
     * Find messages by expression
     *
     * @param Expression $expression
     * @param array      $orderBy
     * @param int        $limit
     * @param int        $offset
     *
     * @return Message[]
     */
    public function findByExpression(Expression $expression, $orderBy = array(), $limit = null, $offset = null)
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder()
            ->select('u')
            ->from('PhlexibleUserBundle:User', 'u');

        if ($offset) {
            $queryBuilder
                ->setFirstResult($offset);
        }

        if ($limit) {
            $queryBuilder
                ->setMaxResults($limit);
        }

        foreach ($orderBy as $field => $dir) {
            $queryBuilder->addOrderBy("u.$field", $dir);
        }

        $this->applyExpression($queryBuilder, $expression);

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * Count messages by expression
     *
     * @param Expression $expression
     *
     * @return int
     */
    public function countByExpression(Expression $expression)
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder()
            ->select('COUNT(u)')
            ->from('PhlexibleUserBundle:User', 'u');

        $this->applyExpression($queryBuilder, $expression);

        return $queryBuilder->getQuery()->getSingleScalarResult();
    }

    /**
     * Find message by expression
     *
     * @param Expression $expression
     * @param array      $orderBy
     *
     * @return Message|null
     */
    public function findOneByExpression(Expression $expression, $orderBy = array())
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder()
            ->select('m')
            ->from('PhlexibleUserBundle:User', 'u')
            ->setMaxResults(1);

        foreach ($orderBy as $field => $dir) {
            $queryBuilder->addOrderBy("u.$field", $dir);
        }

        $this->applyExpression($queryBuilder, $expression);

        return $queryBuilder->getQuery()->getSingleResult();
    }

    /**
     * Apply expression
     *
     * @param QueryBuilder $queryBuilder
     * @param Expression   $expression
     */
    private function applyExpression(QueryBuilder $queryBuilder, Expression $expression)
    {
        $visitor = new QueryBuilderExpressionVisitor($queryBuilder, 'u');
        $visitor->apply($expression);
    }
}
