<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\Elementtype\File\Parser;

use FluentDOM\Document;
use Phlexible\Component\Elementtype\Domain\Elementtype;

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
     * @return Elementtype
     */
    public function parseString($xml);

    /**
     * @param Document $dom
     *
     * @return Elementtype
     */
    public function parse(Document $dom);
}
