<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\MetaSet\File\Parser;

use Phlexible\Component\MetaSet\Model\MetaSetInterface;

/**
 * Parser interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface ParserInterface
{
    /**
     * @param string $content
     *
     * @return MetaSetInterface
     */
    public function parse($content);
}
