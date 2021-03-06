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

use Webmozart\Expression\Expression;

/**
 * Expression serializer interface.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface ExpressionSerializerInterface
{
    /**
     * @param Expression $expr
     *
     * @return mixed
     */
    public function serialize(Expression $expr);

    /**
     * @param mixed $expression
     *
     * @return Expression
     */
    public function deserialize(array $expression);
}
