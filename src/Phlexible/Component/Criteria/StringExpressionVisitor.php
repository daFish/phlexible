<?php
/**
 * phlexible
 *
 * @copyright 2007 brainbits GmbH (http://www.brainbits.net)
 * @license   http://www.makeweb.de/LICENCE     Dummy Licence
 */

namespace Phlexible\Component\Criteria;

use Doctrine\Common\Collections\Expr\Comparison;
use Doctrine\Common\Collections\Expr\CompositeExpression;
use Doctrine\Common\Collections\Expr\Expression;
use Doctrine\Common\Collections\Expr\ExpressionVisitor as BaseExpressionVisitor;
use Doctrine\Common\Collections\Expr\Value;

/**
 * String expression visitor
 *
 * @author  Stephan Wentz <sw@brainbits.net>
 */
class StringExpressionVisitor extends BaseExpressionVisitor
{
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
        $field = 'u.' . $comparison->getField();
        $value = $comparison->getValue()->getValue(); // shortcut for walkValue()

        switch ($comparison->getOperator()) {
            case Comparison::EQ:
                return "$field = $value";

            case Comparison::NEQ:
                return "$field != $value";

            case Comparison::LT:
                return "$field < $value";

            case Comparison::LTE:
                return "$field <= $value";

            case Comparison::GT:
                return "$field > $value";

            case Comparison::GTE:
                return "$field >= $value";

            case Comparison::IN:
                return "$field IN ($value)";

            case Comparison::NIN:
                return "$field NIN ($value)";

            case Comparison::CONTAINS:
                return "$field CONTAINS ($value)";

            default:
                throw new \RuntimeException("Unknown comparison operator: " . $comparison->getOperator());
        }
    }

    private function literal($value)
    {
        if (is_array($value)) {
            foreach ($value as $index => $v) {
                $value[$index] = '"' . $v . '"';
            }
        } else {
            $value = '"' . $value . '"';
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
        $parts = array();
        foreach ($expr->getExpressionList() as $child) {
            $parts[] = $this->dispatch($child);
        }

        switch($expr->getType()) {
            case CompositeExpression::TYPE_AND:
                return implode(' AND ', $parts);

            case CompositeExpression::TYPE_OR:
                return implode(' OR ', $parts);

            default:
                throw new \RuntimeException("Unknown composite " . $expr->getType());
        }
    }
}
