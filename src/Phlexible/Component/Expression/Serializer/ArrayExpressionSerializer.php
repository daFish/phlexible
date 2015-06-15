<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\Expression\Serializer;

use Phlexible\Component\Expression\Exception\UnhandledExpressionException;
use Webmozart\Expression\Comparison;
use Webmozart\Expression\Expression;
use Webmozart\Expression\Logic;
use Webmozart\Expression\Selector;

/**
 * Array expression serializer
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ArrayExpressionSerializer implements ExpressionSerializer
{
    /**
     * {@inheritdoc}
     *
     * @return array
     */
    public function serialize(Expression $expr)
    {
        $data = $this->serializeExpression($expr);

        return $data;
    }

    /**
     * {@inheritdoc}
     *
     * @return Expression
     */
    public function deserialize(array $expression)
    {
        return $this->deserializeExpression($expression);
    }

    /**
     * @param Expression $expr
     *
     * @return array
     */
    private function serializeExpression(Expression $expr)
    {
        $class = get_class($expr);

        if ($expr instanceof Logic\Conjunction) {
            $data = array('op' => 'and', 'expressions' => array());
            foreach ($expr->getConjuncts() as $conjunct) {
                $data['expressions'][] = $this->serializeExpression($conjunct);
            }
        } elseif ($expr instanceof Logic\Disjunction) {
            $data = array('op' => 'or', 'expressions' => array());
            foreach ($expr->getDisjuncts() as $conjunct) {
                $data['expressions'][] = $this->serializeExpression($conjunct);
            }
        } elseif ($expr instanceof Logic\Not) {
            $data = array(
                'op' => 'not',
                'expression' => $this->serializeExpression($expr->getNegatedExpression())
            );
        } elseif ($expr instanceof Selector\Key) {
            $field = $expr->getKey();
            $data = array_merge(array('field' => $field), $this->serializeExpression($expr->getExpression()));
        } elseif ($expr instanceof Comparison\Equals) {
            $data = array('op' => 'equals', 'value' => $expr->getComparedValue());
        } elseif ($expr instanceof Comparison\NotEquals) {
            $data = array('op' => 'notEquals', 'value' => $expr->getComparedValue());
        } elseif ($expr instanceof Comparison\Same) {
            $data = array('op' => 'same', 'value' => $expr->getComparedValue());
        } elseif ($expr instanceof Comparison\NotSame) {
            $data = array('op' => 'notSame', 'value' => $expr->getComparedValue());
        } elseif ($expr instanceof Comparison\StartsWith) {
            $data = array('op' => 'startsWith', 'value' => $expr->getAcceptedPrefix());
        } elseif ($expr instanceof Comparison\EndsWith) {
            $data = array('op' => 'endsWith', 'value' => $expr->getAcceptedSuffix());
        } elseif ($expr instanceof Comparison\Contains) {
            $data = array('op' => 'contains', 'value' => $expr->getComparedValue());
        } elseif ($expr instanceof Comparison\Matches) {
            $data = array('op' => 'matches', 'value' => $expr->getRegularExpression());
        } elseif ($expr instanceof Comparison\In) {
            $data = array('op' => 'in', 'value' => $expr->getAcceptedValues());
        } elseif ($expr instanceof Comparison\KeyExists) {
            $data = array('op' => 'keyExists', 'value' => $expr->getKey());
        } elseif ($expr instanceof Comparison\KeyNotExists) {
            $data = array('op' => 'keyNotExists', 'value' => $expr->getKey());
        } elseif ($expr instanceof Comparison\IsEmpty) {
            $data = array('op' => 'isEmpty');
        } elseif ($expr instanceof Comparison\NotEmpty) {
            $data = array('op' => 'isNotEmpty');
        } elseif ($expr instanceof Comparison\GreaterThan) {
            $data = array('op' => 'greaterThan', 'value' => $expr->getComparedValue());
        } elseif ($expr instanceof Comparison\GreaterThanEqual) {
            $data = array('op' => 'greaterThanEqual', 'value' => $expr->getComparedValue());
        } elseif ($expr instanceof Comparison\LessThan) {
            $data = array('op' => 'lessThan', 'value' => $expr->getComparedValue());
        } elseif ($expr instanceof Comparison\LessThanEqual) {
            $data = array('op' => 'lessThanEqual', 'value' => $expr->getComparedValue());
        } else {
            throw new UnhandledExpressionException("Unhandled expression $class");
        }

        return $data;
    }

    /**
     * @param array $expression
     *
     * @return Expression
     */
    private function deserializeExpression(array $expression)
    {
        $op = $expression['op'];

        if ($op === 'and') {
            return $this->deserializeAnd($expression);
        } elseif ($op === 'or') {
            return $this->deserializeOr($expression);
        } elseif ($op === 'not') {
            return $this->deserializeNot($expression);
        } else {
            return $this->deserializeComparison($expression);
        }
    }

    /**
     * @param array $expressions
     *
     * @return Expression[]
     */
    private function deserializeExpressions(array $expressions)
    {
        $data = array();
        foreach ($expressions as $expression) {
            $data[] = $this->deserializeExpression($expression);
        }

        return $data;
    }

    /**
     * @param array $data
     *
     * @return Logic\Conjunction
     */
    private function deserializeAnd($data)
    {
        return new Logic\Conjunction($this->deserializeExpressions($data['expressions']));
    }

    /**
     * @param array $data
     *
     * @return Logic\Disjunction
     */
    private function deserializeOr($data)
    {
        return new Logic\Disjunction($this->deserializeExpressions($data['expressions']));
    }

    /**
     * @param array $data
     *
     * @return Logic\Not
     */
    private function deserializeNot($data)
    {
        return new Logic\Not($this->deserializeExpression($data['expression']));
    }

    /**
     * @param array $comparison
     *
     * @return Expression
     */
    private function deserializeComparison($comparison)
    {
        $op = $comparison['op'];

        switch ($op) {
            case 'equals':
                $expr = new Comparison\Equals($comparison['value']);
                break;

            case 'notEquals':
                $expr = new Comparison\NotEquals($comparison['value']);
                break;

            case 'same':
                $expr = new Comparison\Same($comparison['value']);
                break;

            case 'notSame':
                $expr = new Comparison\NotSame($comparison['value']);
                break;

            case 'startsWith':
                $expr = new Comparison\StartsWith($comparison['value']);
                break;

            case 'endsWith':
                $expr = new Comparison\EndsWith($comparison['value']);
                break;

            case 'contains':
                $expr = new Comparison\Contains($comparison['value']);
                break;

            case 'matches':
                $expr = new Comparison\Matches($comparison['value']);
                break;

            case 'in':
                $expr = new Comparison\In($comparison['value']);
                break;

            case 'keyExists':
                $expr = new Comparison\KeyExists($comparison['value']);
                break;

            case 'keyNotExists':
                $expr = new Comparison\KeyNotExists($comparison['value']);
                break;

            case 'null':
                $expr = new Comparison\Same(null);
                break;

            case 'notNull':
                $expr = new Comparison\NotSame(null);
                break;

            case 'isEmpty':
                $expr = new Comparison\IsEmpty();
                break;

            case 'notEmpty':
                $expr = new Comparison\NotEmpty(null);
                break;

            case 'greaterThan':
                $expr = new Comparison\GreaterThan($comparison['value']);
                break;

            case 'greaterThanEqual':
                $expr = new Comparison\GreaterThanEqual($comparison['value']);
                break;

            case 'lessThan':
                $expr = new Comparison\LessThan($comparison['value']);
                break;

            case 'lessThanEqual':
                $expr = new Comparison\LessThanEqual($comparison['value']);
                break;

            default:
                throw new UnhandledExpressionException("Unhandled op $op");
        }

        if (isset($comparison['field'])) {
            $field = $comparison['field'];
            $expr = new Selector\Key($field, $expr);
        }

        return $expr;
    }
}
