<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\TreeBundle\Node\LinkExtractor;

/**
 * Values extractor interface
 *
 * @author Stephan Wentz <swentz@brainbits.net>
 */
interface ValuesExtractorInterface
{
    /**
     * @param mixed  $content
     * @param string $language
     *
     * @return array
     */
    public function extract($content, $language);
}
