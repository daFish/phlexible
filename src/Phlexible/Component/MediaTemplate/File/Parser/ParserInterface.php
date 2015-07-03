<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\MediaTemplate\File\Parser;

use Phlexible\Component\MediaTemplate\Model\TemplateInterface;

/**
 * Loader interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface ParserInterface
{
    /**
     * @param string $content
     *
     * @return TemplateInterface
     */
    public function parse($content);
}
