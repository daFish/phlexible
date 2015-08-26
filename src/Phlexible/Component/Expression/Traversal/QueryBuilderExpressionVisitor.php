<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\Expression\Traversal;

use Doctrine\ORM\QueryBuilder;
use Phlexible\Component\Expression\Exception\UnhandledExpressionException;
use Phlexible\Component\Expression\Exception\UnsupportedExpressionException;
use SplStack;
use Webmozart\Expression\Comparison;
use Webmozart\Expression\Expression;
use Webmozart\Expression\Logic;
use Webmozart\Expression\Selector;
use Webmozart\Expression\Traversal\ExpressionTraverser;
use Webmozart\Expression\Traversal\ExpressionVisitor;

/**
 * Expression visitor
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class QueryBuilderExpressionVisitor implements ExpressionVisitor
{
    /**
     * @var QueryBuilder
     */
    private $qb;

    /**
     * @var string
     */
    private $alias;

    /**
     * @var string
     */
    private $currentField;

    /**
     * @var SplStack
     */
    private $currentStack;

    /**
     * @param QueryBuilder $qb
     * @param string       $alias
     */
    public function __construct(QueryBuilder $qb, $alias)
    {
        $this->qb = $qb;
        $this->alias = $alias;
    }

    /**
     * Applies the given expression to a query builder
     *
     * @param Expression $expr
     */
    public function apply(Expression $expr)
    {
        $this->currentStack = new SplStack();
        $this->currentStack->push(new SplStack());

        $traverser = new ExpressionTraverser();
        $traverser->addVisitor($this);
        $traverser->traverse($expr);

        $this->qb->andWhere($this->currentStack->pop()->pop());
    }

    /**
     * {@inheritdoc}
     */
    public function enterExpression(Expression $expr)
    {
        if ($expr instanceof Logic\Conjunction) {
            $this->currentStack->push(new SplStack());
        } elseif ($expr instanceof Logic\Disjunction) {
            $this->currentStack->push(new SplStack());
        } elseif ($expr instanceof Logic\Not) {
            $this->currentStack->push(new SplStack());
        } elseif ($expr instanceof Selector\Key) {
            if ($this->currentField) {
                throw new UnsupportedExpressionException("Sub-keys not supported.");
            }
            $this->currentField = $expr->getKey();
        } elseif (
            $expr instanceof Selector\All ||
            $expr instanceof Selector\AtLeast ||
            $expr instanceof Selector\AtMost ||
            $expr instanceof Selector\Exactly
        ) {
            throw new UnsupportedExpressionException("Selector " . get_class($expr) . " not supported.");
        }

        return $expr;
    }

    /**
     * {@inheritdoc}
     */
    public function leaveExpression(Expression $expr)
    {
        if ($expr instanceof Logic\Conjunction) {
            $conjunctionStack = $this->currentStack->pop();
            $current = $this->qb->expr()->andX();
            while ($conjunctionStack->count()) {
                $current->add($conjunctionStack->pop());
            }
            $this->currentStack->top()->push($current);
        } elseif ($expr instanceof Logic\Disjunction) {
            $disjunctionStack = $this->currentStack->pop();
            $current = $this->qb->expr()->orX();
            while ($disjunctionStack->count()) {
                $current->add($disjunctionStack->pop());
            }
            $this->currentStack->top()->push($current);
        } elseif ($expr instanceof Logic\Not) {
            $negatedStack = $this->currentStack->pop();
            $current = $this->qb->expr()->not($negatedStack->pop());
            $this->currentStack->top()->push($current);
        } elseif ($expr instanceof Selector\Key) {
            $this->currentField = null;
        } else {
            $this->currentStack->top()->push($this->walkComparison($expr));
        }

        return $expr;
    }

    /**
     * {@inheritDoc}
     */
    public function walkComparison(Expression $expr)
    {
        if ($expr instanceof Logic\AlwaysTrue) {
            return $this->qb->expr()->eq(1, 1);
        } elseif ($expr instanceof Logic\AlwaysFalse) {
            return $this->qb->expr()->eq(1, 0);
        }

        if (!$this->currentField) {
            throw new UnsupportedExpressionException("Comparison without field not supported.");
        }

        $field = $this->alias . '.' . $this->currentField;

        if ($expr instanceof Comparison\Contains) {
            return $this->qb->expr()->like($field, $this->literal("%{$expr->getComparedValue()}%"));
        } elseif ($expr instanceof Comparison\EndsWith) {
            return $this->qb->expr()->eq($field, $this->literal("%{$expr->getAcceptedSuffix()}"));
        } elseif ($expr instanceof Comparison\Equals) {
            return $this->qb->expr()->eq($field, $this->literal($expr->getComparedValue()));
        } elseif ($expr instanceof Comparison\GreaterThan) {
            return $this->qb->expr()->gt($field, $this->literal($expr->getComparedValue()));
        } elseif ($expr instanceof Comparison\GreaterThanEqual) {
            return $this->qb->expr()->gte($field, $this->literal($expr->getComparedValue()));
        } elseif ($expr instanceof Comparison\In) {
            return $this->qb->expr()->in($field, $this->literal($expr->getAcceptedValues()));
        } elseif ($expr instanceof Comparison\IsEmpty) {
            throw new UnsupportedExpressionException("Comparison " . get_class($expr) . " not supported.");
        } elseif ($expr instanceof Comparison\KeyExists) {
            throw new UnsupportedExpressionException("Comparison " . get_class($expr) . " not supported.");
        } elseif ($expr instanceof Comparison\KeyNotExists) {
            throw new UnsupportedExpressionException("Comparison " . get_class($expr) . " not supported.");
        } elseif ($expr instanceof Comparison\LessThan) {
            return $this->qb->expr()->lt($field, $this->literal($expr->getComparedValue()));
        } elseif ($expr instanceof Comparison\LessThanEqual) {
            return $this->qb->expr()->lte($field, $this->literal($expr->getComparedValue()));
        } elseif ($expr instanceof Comparison\Matches) {
            throw new UnsupportedExpressionException("Comparison " . get_class($expr) . " not supported.");
        } elseif ($expr instanceof Comparison\NotEmpty) {
            throw new UnsupportedExpressionException("Comparison " . get_class($expr) . " not supported.");
        } elseif ($expr instanceof Comparison\NotEquals) {
            return $this->qb->expr()->neq($field, $this->literal($expr->getComparedValue()));
        } elseif ($expr instanceof Comparison\NotSame) {
            return $this->qb->expr()->neq($field, $this->literal($expr->getComparedValue()));
        } elseif ($expr instanceof Comparison\Same) {
            return $this->qb->expr()->eq($field, $this->literal($expr->getComparedValue()));
        } elseif ($expr instanceof Comparison\StartsWith) {
            return $this->qb->expr()->like($field, $this->literal("{$expr->getAcceptedPrefix()}%"));
        } else {
            throw new UnhandledExpressionException("Comparison " . get_class($expr) . " not handled.");
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
}
