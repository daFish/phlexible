<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\MessageBundle\Serialization;

use JMS\Serializer\Handler\SubscribingHandlerInterface;
use JMS\Serializer\GraphNavigator;
use JMS\Serializer\JsonDeserializationVisitor;
use JMS\Serializer\JsonSerializationVisitor;
use JMS\Serializer\Context;
use Phlexible\Component\Expression\Serializer\ArrayExpressionSerializer;
use Webmozart\Expression\Expression;

/**
 * Expression handler
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ExpressionHandler implements SubscribingHandlerInterface
{
    /**
     * @var ArrayExpressionSerializer
     */
    private $serializer;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->serializer = new ArrayExpressionSerializer();
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribingMethods()
    {
        return array(
            array(
                'direction' => GraphNavigator::DIRECTION_SERIALIZATION,
                'format'    => 'json',
                'type'      => 'Webmozart\Expression\Logic\Disjunction',
                'method'    => 'serializeExpressionToJson',
            ),
            array(
                'direction' => GraphNavigator::DIRECTION_SERIALIZATION,
                'format'    => 'json',
                'type'      => 'Webmozart\Expression\Logic\Conjunction',
                'method'    => 'serializeExpressionToJson',
            ),
            array(
                'direction' => GraphNavigator::DIRECTION_DESERIALIZATION,
                'format'    => 'json',
                'type'      => 'Webmozart\Expression\Logic\Disjunction',
                'method'    => 'deserializeJsonToExpression',
            ),
            array(
                'direction' => GraphNavigator::DIRECTION_DESERIALIZATION,
                'format'    => 'json',
                'type'      => 'Webmozart\Expression\Logic\Conjunction',
                'method'    => 'deserializeJsonToExpression',
            ),
        );
    }

    /**
     * @param JsonSerializationVisitor $visitor
     * @param Expression               $expression
     * @param array                    $type
     * @param Context                  $context
     *
     * @return array
     */
    public function serializeExpressionToJson(JsonSerializationVisitor $visitor, Expression $expression, array $type, Context $context)
    {
        return $this->serializer->serialize($expression);
    }

    /**
     * @param JsonDeserializationVisitor $visitor
     * @param array                      $json
     * @param array                      $type
     * @param Context                    $context
     *
     * @return Expression
     */
    public function deserializeJsonToExpression(JsonDeserializationVisitor $visitor, array $json, array $type, Context $context)
    {
        return $this->serializer->deserialize($json);
    }
}
