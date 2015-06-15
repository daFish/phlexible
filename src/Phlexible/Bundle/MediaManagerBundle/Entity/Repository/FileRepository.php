<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\MediaManagerBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Phlexible\Bundle\MessageBundle\Entity\Message;
use Phlexible\Component\Expression\Traversal\QueryBuilderExpressionVisitor;
use Webmozart\Expression\Expression;

/**
 * File repository
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class FileRepository extends EntityRepository
{
    /**
     * @param array $criteria
     *
     * @return int
     */
    public function countBy(array $criteria)
    {
        return 0;
    }

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
            ->select('f')
            ->from('PhlexibleMediaManagerBundle:File', 'f');

        if ($offset) {
            $queryBuilder
                ->setFirstResult($offset);
        }

        if ($limit) {
            $queryBuilder
                ->setMaxResults($limit);
        }

        foreach ($orderBy as $field => $dir) {
            $queryBuilder->addOrderBy("f.$field", $dir);
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
            ->select('COUNT(f)')
            ->from('PhlexibleMediaManagerBundle:File', 'f');

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
            ->select('f')
            ->from('PhlexibleMediaManagerBundle:File', 'f')
            ->setMaxResults(1);

        foreach ($orderBy as $field => $dir) {
            $queryBuilder->addOrderBy("f.$field", $dir);
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
        $visitor = new QueryBuilderExpressionVisitor($queryBuilder, 'f');
        $visitor->apply($expression);
    }
}
