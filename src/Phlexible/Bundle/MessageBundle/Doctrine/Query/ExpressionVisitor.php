<?php
/**
 * phlexible
 *
 * @copyright 2007 brainbits GmbH (http://www.brainbits.net)
 * @license   http://www.makeweb.de/LICENCE     Dummy Licence
 */

namespace Phlexible\Bundle\MessageBundle\Doctrine\Query;

use Doctrine\Common\Collections\Expr\Comparison;
use Doctrine\Common\Collections\Expr\CompositeExpression;
use Doctrine\Common\Collections\Expr\Expression;
use Doctrine\Common\Collections\Expr\ExpressionVisitor as BaseExpressionVisitor;
use Doctrine\Common\Collections\Expr\Value;
use Doctrine\ORM\QueryBuilder;

/**
 * Expression visitor
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ExpressionVisitor extends BaseExpressionVisitor
{
    /**
     * @var QueryBuilder
     */
    private $qb;

    /**
     * @param QueryBuilder $qb
     */
    public function __construct(QueryBuilder $qb)
    {
        $this->qb = $qb;
    }

    /**
     * Dispatches walking an expression to the appropriate handler.
     *
     * @param Expression $expr
     *
     * @return mixed
     *
     * @throws \RuntimeException
     */
    public function dispatch(Expression $expr)
    {
        switch (true) {
            case ($expr instanceof Comparison):
                return $this->walkComparison($expr);

            case ($expr instanceof Value):
                return $this->walkValue($expr);

            case ($expr instanceof CompositeExpression):
                return $this->walkCompositeExpression($expr);

            default:
                throw new \RuntimeException("Unknown Expression " . get_class($expr));
        }
    }

    /**
     * {@inheritDoc}
     */
    public function walkComparison(Comparison $comparison)
    {
        $field = 'm.' . $comparison->getField();
        $value = $comparison->getValue()->getValue(); // shortcut for walkValue()

        switch ($comparison->getOperator()) {
            case Comparison::EQ:
                return $this->qb->expr()->eq($field, $this->literal($value));

            case Comparison::NEQ:
                return $this->qb->expr()->neq($field, $this->literal($value));

            case Comparison::LT:
                return $this->qb->expr()->lt($field, $value);

            case Comparison::LTE:
                return $this->qb->expr()->lte($field, $value);

            case Comparison::GT:
                return $this->qb->expr()->gt($field, $value);

            case Comparison::GTE:
                return $this->qb->expr()->gte($field, $value);

            case Comparison::IN:
                return $this->qb->expr()->in($field, $this->literal($value));

            case Comparison::NIN:
                return $this->qb->expr()->notIn($field, $this->literal($value));

            case Comparison::CONTAINS:
                return $this->qb->expr()->like($field, $this->literal("%$value%"));

            default:
                throw new \RuntimeException("Unknown comparison operator: " . $comparison->getOperator());
        }
    }

    private function literal($value)
    {
        if (is_array($value)) {
            foreach ($value as $index => $v) {
                $value[$index] = $this->qb->expr()->literal($v);
            }
        } else {
            $value = $this->qb->expr()->literal($value);
        }

        return $value;
    }

    /**
     * {@inheritDoc}
     */
    public function walkValue(Value $value)
    {
        return $value->getValue();
    }

    /**
     * {@inheritDoc}
     */
    public function walkCompositeExpression(CompositeExpression $expr)
    {
        switch($expr->getType()) {
            case CompositeExpression::TYPE_AND:
                $composite = $this->qb->expr()->andX();
                break;

            case CompositeExpression::TYPE_OR:
                $composite = $this->qb->expr()->orX();
                break;

            default:
                throw new \RuntimeException("Unknown composite " . $expr->getType());
        }

        foreach ($expr->getExpressionList() as $child) {
            $composite->add($this->dispatch($child));
        }

        return $composite;
    }
}
