<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\Site\File\Parser;

use FluentDOM\Document;
use Phlexible\Component\Site\Domain\Site;

/**
 * Parser interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface ParserInterface
{
    /**
     * @param string $xml
     *
     * @return Site
     */
    public function parseString($xml);

    /**
     * @param Document $dom
     *
     * @return Site
     */
    public function parse(Document $dom);
}
