<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\Site\File\Parser;

use FluentDOM\Document;
use Phlexible\Component\Site\Domain\Site;

/**
 * Parser interface.
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
