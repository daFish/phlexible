<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\Expression\Transformer;

use Phlexible\Component\Expression\Serializer\ArrayExpressionSerializer;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Webmozart\Expression\Expression;

/**
 * Expression transformer
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ExpressionTransformer implements DataTransformerInterface
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
     * Transforms an object (issue) to a string (number).
     *
     * @param Expression|null $expression
     *
     * @return string
     */
    public function transform($expression)
    {
        if (null === $expression) {
            return '';
        }

        return $this->serializer->serialize($expression);
    }

    /**
     * Transforms a string (number) to an object (issue).
     *
     * @param array $expression
     *
     * @return Expression|null
     * @throws TransformationFailedException if object (issue) is not found.
     */
    public function reverseTransform($expression)
    {
        if (!$expression) {
            return null;
        }

        $expression = $this->serializer->deserialize($expression);

        if (null === $expression) {
            throw new TransformationFailedException(sprintf(
                'An issue with number "%s" does not exist!',
                $expression
            ));
        }

        return $expression;
    }
}
