<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaManagerBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Phlexible\Bundle\MessageBundle\Entity\Message;
use Phlexible\Component\Expression\Traversal\QueryBuilderExpressionVisitor;
use Webmozart\Expression\Expression;

/**
 * Folder repository
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class FolderRepository extends EntityRepository
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
            ->from('PhlexibleMediaManagerBundle:Folder', 'f');

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
            ->from('PhlexibleMediaManagerBundle:Folder', 'f');

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
            ->from('PhlexibleMediaManagerBundle:Folder', 'f')
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
