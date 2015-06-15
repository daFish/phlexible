<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\MessageBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Phlexible\Bundle\MessageBundle\Entity\Message;
use Phlexible\Component\Expression\Traversal\QueryBuilderExpressionVisitor;
use Webmozart\Expression\Expression;

/**
 * Message repository
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class MessageRepository extends EntityRepository
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
            ->select('m')
            ->from('PhlexibleMessageBundle:Message', 'm');

        if ($offset) {
            $queryBuilder
                ->setFirstResult($offset);
        }

        if ($limit) {
            $queryBuilder
                ->setMaxResults($limit);
        }

        foreach ($orderBy as $field => $dir) {
            $queryBuilder->addOrderBy("m.$field", $dir);
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
            ->select('COUNT(m)')
            ->from('PhlexibleMessageBundle:Message', 'm');

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
            ->from('PhlexibleMessageBundle:Message', 'm')
            ->setMaxResults(1);

        foreach ($orderBy as $field => $dir) {
            $queryBuilder->addOrderBy("m.$field", $dir);
        }

        $this->applyExpression($queryBuilder, $expression);

        return $queryBuilder->getQuery()->getSingleResult();
    }

    /**
     * @return array
     */
    public function getFacets()
    {
        $channels = $this->createQueryBuilder('m')->select('DISTINCT m.channel')->getQuery()->getScalarResult();
        $types = $this->createQueryBuilder('m')->select('DISTINCT m.type')->getQuery()->getScalarResult();
        $roles = $this->createQueryBuilder('m')->select('DISTINCT m.role')->getQuery()->getScalarResult();

        return [
            'channels' => array_column($channels, 'channel'),
            'types'    => array_column($types, 'type'),
            'roles'    => array_column($roles, 'role'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getFacetsByExpression(Expression $expression)
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder()
            ->select('m')
            ->from('PhlexibleMessageBundle:Message', 'm');

        $this->applyExpression($queryBuilder, $expression);

        $channelsQb = clone $queryBuilder;
        $fullChannels = $channelsQb->select('DISTINCT m.channel')->getQuery()->getScalarResult();

        $typeQb = clone $queryBuilder;
        $fullTypes = $typeQb->select('DISTINCT m.type')->getQuery()->getScalarResult();

        $roleQb = clone $queryBuilder;
        $fullRoles = $roleQb->select('DISTINCT m.role')->getQuery()->getScalarResult();

        $channels = array();
        foreach ($fullChannels as $channel) {
            $channels[] = $channel['channel'] ?: '-';
        }
        sort($channels);

        $roles = array();
        foreach ($fullRoles as $role) {
            $roles[] = $role['role'] ?: '-';
        }
        sort($roles);

        return [
            'types'      => array_map(function($v) {
                return (int) $v;
            }, array_column($fullTypes, 'type')),
            'channels'   => $channels,
            'roles'      => $roles,
        ];
    }

    /**
     * Apply expression
     *
     * @param QueryBuilder $queryBuilder
     * @param Expression   $expression
     */
    private function applyExpression(QueryBuilder $queryBuilder, Expression $expression)
    {
        $visitor = new QueryBuilderExpressionVisitor($queryBuilder, 'm');
        $visitor->apply($expression);
    }
}
