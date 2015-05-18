<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\Expression\Serializer;

use Webmozart\Expression\Comparison;
use Webmozart\Expression\Expression;
use Webmozart\Expression\Logic;
use Webmozart\Expression\Selector;

/**
 * Expression serializer interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface ExpressionSerializer
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
